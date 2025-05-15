<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\OrderInvoiceCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class OrderInvoiceController extends PimcoreController
{
    public function getInvoiceAbleItemsAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $itemsToReturn = [];

        if (!$this->getProcessableHelper()->isProcessable($order)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'The current order state does not allow to create invoices']);
        }

        try {
            $items = $this->getProcessableHelper()->getProcessableItems($order);
        } catch (\Exception $e) {
            return $this->viewHandler->handle(['success' => false, 'message' => $e->getMessage()]);
        }

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

                $itemsToReturn[] = $event->getArguments();
            }
        }

        return $this->viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createInvoiceAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');

        $form = $this->container->get('form.factory')->createNamed('', OrderInvoiceCreationType::class);

        $handledForm = $form->handleRequest($request);

        if ($request->getMethod() === 'POST') {
            if (!$handledForm->isValid()) {
                return $this->viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $this->container->get(ErrorSerializer::class)->serializeErrorFromHandledForm($form),
                    ],
                );
            }

            $resource = $handledForm->getData();

            $order = $this->getOrderRepository()->find($orderId);

            if (!$order instanceof OrderInterface) {
                return $this->viewHandler->handle([
                    'success' => false,
                    'message' => "Order with ID '$orderId' not found",
                ]);
            }

            try {
                // request invoice ready state from order, if it's our first invoice.
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

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    public function updateStateAction(Request $request): JsonResponse
    {
        $invoiceId = $this->getParameterFromRequest($request, 'id');
        $invoice = $this->getOrderInvoiceRepository()->find($invoiceId);
        $transition = $this->getParameterFromRequest($request, 'transition');

        if (!$invoice instanceof OrderInvoiceInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'invalid shipment']);
        }

        //apply state machine
        $workflow = $this->getStateMachineManager()->get($invoice, InvoiceStates::IDENTIFIER);
        if (!$workflow->can($invoice, $transition)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($invoice, $transition);

        return $this->viewHandler->handle(['success' => true]);
    }

    public function renderAction(Request $request): Response
    {
        $invoiceId = $this->getParameterFromRequest($request, 'id');
        $invoice = $this->getOrderInvoiceRepository()->find($invoiceId);

        if ($invoice instanceof OrderInvoiceInterface) {
            try {
                $responseData = $this->getOrderDocumentRenderer()->renderDocumentPdf($invoice);
                $header = [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="invoice-' . $invoice->getId() . '.pdf"',
                ];
            } catch (\Exception $e) {
                $responseData = '<strong>' . $e->getMessage() . '</strong><br>trace: ' . $e->getTraceAsString();
                $header = ['Content-Type' => 'text/html'];
            }

            return new Response($responseData, 200, $header);
        }

        throw new NotFoundHttpException(sprintf('Invoice with Id %s not found', $invoiceId));
    }

    private function getProcessableHelper(): ProcessableInterface
    {
        return $this->container->get('coreshop.order.invoice.processable');
    }

    private function getOrderRepository(): OrderRepositoryInterface
    {
        return $this->container->get('coreshop.repository.order');
    }

    private function getOrderDocumentRenderer(): OrderDocumentRendererInterface
    {
        return $this->container->get('coreshop.renderer.order.pdf');
    }

    private function getOrderInvoiceRepository(): OrderInvoiceRepositoryInterface
    {
        return $this->container->get('coreshop.repository.order_invoice');
    }

    private function getInvoiceFactory(): PimcoreFactoryInterface
    {
        return $this->container->get('coreshop.factory.order_invoice');
    }

    private function getOrderToInvoiceTransformer(): OrderDocumentTransformerInterface
    {
        return $this->container->get('coreshop.order.transformer.order_to_invoice');
    }

    protected function getStateMachineManager(): StateMachineManager
    {
        return $this->container->get(StateMachineManagerInterface::class);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
                new SubscribedService('coreshop.repository.order', ProcessableInterface::class, attributes: new Autowire(service:'coreshop.repository.order')),
                new SubscribedService('event_dispatcher', EventDispatcherInterface::class),
                new SubscribedService(ErrorSerializer::class, ErrorSerializer::class),
                new SubscribedService('coreshop.order.invoice.processable', NoteServiceInterface::class, attributes: new Autowire(service:'coreshop.order.invoice.processable')),
                new SubscribedService('coreshop.renderer.order.pdf', OrderDocumentRendererInterface::class, attributes: new Autowire(service:'coreshop.renderer.order.pdf')),
                new SubscribedService('coreshop.factory.order_invoice', FactoryInterface::class, attributes: new Autowire(service:'coreshop.factory.order_invoice')),
                new SubscribedService('coreshop.order.transformer.order_to_invoice', OrderDocumentTransformerInterface::class, attributes: new Autowire('@CoreShop\Component\Order\Transformer\OrderToInvoiceTransformer')),
                new SubscribedService('coreshop.repository.order_invoice', OrderInvoiceRepositoryInterface::class, attributes: new Autowire(service:'coreshop.repository.order_invoice')),
                new SubscribedService(StateMachineManagerInterface::class, StateMachineManagerInterface::class),
            ]);
    }
}
