<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use Carbon\Carbon;
use CoreShop\Bundle\OrderBundle\Form\Type\Studio\OrderInvoiceCreationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Studio\OrderShipmentCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderPaymentInterface;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Order\Transformer\OrderToShipmentTransformer;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Component\Payment\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class StudioOrderDocumentController extends AdminController
{
    public function getInvoiceAbleItemsAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        if (!$this->getInvoiceProcessableHelper()->isProcessable($order)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'The current order state does not allow to create invoices']);
        }

        try {
            $items = $this->getInvoiceProcessableHelper()->getProcessableItems($order);
        } catch (\Exception $e) {
            return $this->viewHandler->handle(['success' => false, 'message' => $e->getMessage()]);
        }

        $itemsToReturn = [];

        foreach ($items as $item) {
            $orderItem = $item['item'];
            if ($orderItem instanceof OrderItemInterface) {
                $itemToReturn = [
                    'orderItemId' => $orderItem->getId(),
                    'price' => $orderItem->getItemPrice(),
                    'maxToInvoice' => $item['quantity'],
                    'quantity' => $orderItem->getQuantity(),
                    'quantityInvoiced' => $orderItem->getQuantity() - $item['quantity'],
                    'toInvoice' => $item['quantity'],
                    'tax' => $orderItem->getTotalTax(),
                    'total' => $orderItem->getTotal(),
                    'name' => $orderItem->getName(),
                ];

                $event = new GenericEvent($orderItem, $itemToReturn);
                $this->container->get('event_dispatcher')->dispatch($event, 'coreshop.order.invoice.prepare_invoice_able');

                $itemsToReturn[$orderItem->getId()] = $event->getArguments();
            }
        }

        return $this->viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createInvoiceAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $processableItems = $this->getInvoiceProcessableHelper()->getProcessableItems($order);
        $allowedData = [
            'id' => $orderId,
            'items' => array_combine(
                array_map(static fn ($item): mixed => $item['item']->getId(), $processableItems),
                array_map(static fn ($item) => [
                    'orderItemId' => $item['item']->getId(),
                    'maxQuantity' => $item['quantity'],
                ], $processableItems),
            ),
        ];

        $form = $this->container->get('form.factory')->createNamed('', OrderInvoiceCreationType::class, $allowedData);
        $handledForm = $form->handleRequest($request);

        if (!$handledForm->isValid()) {
            return $this->viewHandler->handle([
                'success' => false,
                'message' => implode("\n", $this->container->get(ErrorSerializer::class)->serializeErrorFromHandledForm($form)),
            ]);
        }

        $resource = $handledForm->getData();

        try {
            $workflow = $this->getStateMachineManager()->get($order, 'coreshop_order_invoice');
            if ($workflow->can($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE)) {
                $workflow->apply($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE);
            }

            $invoice = $this->getInvoiceFactory()->createNew();
            $invoice->setState(InvoiceStates::STATE_NEW);

            foreach ($resource as $key => $value) {
                if (in_array($key, ['items', 'id', 'state'])) {
                    continue;
                }

                $invoice->setValue($key, $value);
            }

            $items = $resource['items'];
            $invoice = $this->getOrderToInvoiceTransformer()->transform($order, $invoice, $items);

            return $this->viewHandler->handle(['success' => true, 'invoiceId' => $invoice->getId()]);
        } catch (\Exception $ex) {
            return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
        }
    }

    public function getShipAbleItemsAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        if (!$this->getShipmentProcessableHelper()->isProcessable($order)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'The current order state does not allow to create shipments']);
        }

        try {
            $items = $this->getShipmentProcessableHelper()->getProcessableItems($order);
        } catch (\Exception $e) {
            return $this->viewHandler->handle(['success' => false, 'message' => $e->getMessage()]);
        }

        $itemsToReturn = [];

        foreach ($items as $item) {
            $orderItem = $item['item'];
            if ($orderItem instanceof OrderItemInterface) {
                $itemToReturn = [
                    'orderItemId' => $orderItem->getId(),
                    'price' => $orderItem->getItemPrice(),
                    'maxToShip' => $item['quantity'],
                    'quantity' => $orderItem->getQuantity(),
                    'quantityShipped' => $orderItem->getQuantity() - $item['quantity'],
                    'toShip' => $item['quantity'],
                    'tax' => $orderItem->getTotalTax(),
                    'total' => $orderItem->getTotal(),
                    'name' => $orderItem->getName(),
                ];

                $event = new GenericEvent($orderItem, $itemToReturn);
                $this->container->get('event_dispatcher')->dispatch($event, 'coreshop.order.shipment.prepare_ship_able');

                $itemsToReturn[$orderItem->getId()] = $event->getArguments();
            }
        }

        return $this->viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createShipmentAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $processableItems = $this->getShipmentProcessableHelper()->getProcessableItems($order);
        $allowedData = [
            'id' => $orderId,
            'items' => array_combine(
                array_map(static fn ($item): mixed => $item['item']->getId(), $processableItems),
                array_map(static fn ($item) => [
                    'orderItemId' => $item['item']->getId(),
                    'maxQuantity' => $item['quantity'],
                ], $processableItems),
            ),
        ];

        $form = $this->container->get('form.factory')->createNamed('', OrderShipmentCreationType::class, $allowedData);
        $handledForm = $form->handleRequest($request);

        if (!$handledForm->isValid()) {
            return $this->viewHandler->handle([
                'success' => false,
                'message' => implode("\n", $this->container->get(ErrorSerializer::class)->serializeErrorFromHandledForm($form)),
            ]);
        }

        $resource = $handledForm->getData();

        try {
            $workflow = $this->getStateMachineManager()->get($order, 'coreshop_order_shipment');
            if ($workflow->can($order, OrderShipmentTransitions::TRANSITION_REQUEST_SHIPMENT)) {
                $workflow->apply($order, OrderShipmentTransitions::TRANSITION_REQUEST_SHIPMENT);
            }

            $shipment = $this->getShipmentFactory()->createNew();
            $shipment->setState(ShipmentStates::STATE_NEW);

            foreach ($resource as $key => $value) {
                if (in_array($key, ['items', 'id', 'state'])) {
                    continue;
                }

                $shipment->setValue($key, $value);
            }

            $items = $resource['items'];
            $shipment = $this->getOrderToShipmentTransformer()->transform($order, $shipment, $items);

            return $this->viewHandler->handle(['success' => true, 'shipmentId' => $shipment->getId()]);
        } catch (\Exception $ex) {
            return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
        }
    }

    public function addPaymentAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);
        $amount = (int) round($this->getParameterFromRequest($request, 'amount', 0) * $this->getParameter('coreshop.currency.decimal_factor'));
        $paymentProviderId = $this->getParameterFromRequest($request, 'paymentProvider');

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $payments = $this->getPaymentRepository()->findForPayable($order);
        $paymentProvider = $this->getPaymentProviderRepository()->find($paymentProviderId);
        $totalPayed = array_sum(array_map(static function (PaymentInterface $payment) {
            $state = $payment->getState();
            if ($state === PaymentInterface::STATE_CANCELLED ||
                $state === PaymentInterface::STATE_FAILED ||
                $state === PaymentInterface::STATE_REFUNDED) {
                return 0;
            }

            return $payment->getTotalAmount();
        }, $payments));

        if ($paymentProvider instanceof PaymentProviderInterface) {
            $totalPaymentWouldBe = $totalPayed + $amount;

            if ($totalPaymentWouldBe > $order->getTotal()) {
                return $this->viewHandler->handle([
                    'success' => false,
                    'message' => 'Payed Amount is greater than order amount',
                ]);
            }

            $tokenGenerator = new UniqueTokenGenerator(true);
            $uniqueId = $tokenGenerator->generate(15);
            $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

            /**
             * @var PaymentInterface $payment
             */
            $payment = $this->getPaymentFactory()->createNew();
            $payment->setNumber($orderNumber);
            $payment->setPaymentProvider($paymentProvider);

            if (method_exists($payment, 'setCurrency')) {
                $payment->setCurrency($order->getBaseCurrency());
            }

            $payment->setTotalAmount($amount);
            $payment->setState(PaymentInterface::STATE_NEW);
            $payment->setDatePayment(Carbon::now());

            if ($payment instanceof OrderPaymentInterface) {
                $payment->setOrder($order);
            }

            $this->getEntityManager()->persist($payment);
            $this->getEntityManager()->flush();

            $workflow = $this->getStateMachineManager()->get($payment, 'coreshop_payment');
            $workflow->apply($payment, PaymentTransitions::TRANSITION_PROCESS);

            return $this->viewHandler->handle([
                'success' => true,
                'totalPayed' => $totalPayed,
            ]);
        }

        return $this->viewHandler->handle([
            'success' => false,
            'message' => sprintf('Payment Provider %s not found', $paymentProviderId),
        ]);
    }

    private function getOrderRepository(): OrderRepositoryInterface
    {
        return $this->container->get('coreshop.repository.order');
    }

    private function getInvoiceProcessableHelper(): ProcessableInterface
    {
        return $this->container->get('coreshop.order.invoice.processable');
    }

    private function getShipmentProcessableHelper(): ProcessableInterface
    {
        return $this->container->get('coreshop.order.shipment.processable');
    }

    private function getInvoiceFactory(): PimcoreFactoryInterface
    {
        return $this->container->get('coreshop.factory.order_invoice');
    }

    private function getShipmentFactory(): FactoryInterface
    {
        return $this->container->get('coreshop.factory.order_shipment');
    }

    private function getOrderToInvoiceTransformer(): OrderDocumentTransformerInterface
    {
        return $this->container->get('coreshop.order.transformer.order_to_invoice');
    }

    private function getOrderToShipmentTransformer(): OrderDocumentTransformerInterface
    {
        return $this->container->get(OrderToShipmentTransformer::class);
    }

    private function getPaymentRepository(): PaymentRepositoryInterface
    {
        return $this->container->get('coreshop.repository.payment');
    }

    private function getPaymentProviderRepository(): PaymentProviderRepositoryInterface
    {
        return $this->container->get('coreshop.repository.payment_provider');
    }

    private function getPaymentFactory(): FactoryInterface
    {
        return $this->container->get('coreshop.factory.payment');
    }

    private function getEntityManager(): EntityManager
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    protected function getStateMachineManager(): StateMachineManager
    {
        return $this->container->get(StateMachineManagerInterface::class);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            new SubscribedService(StateMachineManagerInterface::class, StateMachineManagerInterface::class),
            new SubscribedService('coreshop.repository.order', OrderRepositoryInterface::class),
            new SubscribedService('event_dispatcher', EventDispatcherInterface::class),
            new SubscribedService(ErrorSerializer::class, ErrorSerializer::class),
            new SubscribedService('coreshop.order.invoice.processable', ProcessableInterface::class, attributes: new Autowire(service: 'coreshop.order.invoice.processable')),
            new SubscribedService('coreshop.order.shipment.processable', ProcessableInterface::class, attributes: new Autowire(service: 'coreshop.order.shipment.processable')),
            new SubscribedService('coreshop.factory.order_invoice', PimcoreFactoryInterface::class, attributes: new Autowire(service: 'coreshop.factory.order_invoice')),
            new SubscribedService('coreshop.factory.order_shipment', FactoryInterface::class, attributes: new Autowire(service: 'coreshop.factory.order_shipment')),
            new SubscribedService('coreshop.order.transformer.order_to_invoice', OrderDocumentTransformerInterface::class, attributes: new Autowire('@CoreShop\Component\Order\Transformer\OrderToInvoiceTransformer')),
            new SubscribedService(OrderToShipmentTransformer::class, OrderToShipmentTransformer::class),
            new SubscribedService('coreshop.repository.payment', PaymentRepositoryInterface::class),
            new SubscribedService('coreshop.repository.payment_provider', PaymentProviderRepositoryInterface::class),
            new SubscribedService('coreshop.factory.payment', FactoryInterface::class, attributes: new Autowire(service: 'coreshop.factory.payment')),
            new SubscribedService('doctrine.orm.entity_manager', EntityManager::class, attributes: new Autowire(service: 'doctrine.orm.entity_manager')),
        ]);
    }
}
