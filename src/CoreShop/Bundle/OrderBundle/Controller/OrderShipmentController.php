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

use CoreShop\Bundle\OrderBundle\Form\Type\OrderShipmentCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Order\Transformer\OrderToShipmentTransformer;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class OrderShipmentController extends PimcoreController
{
    public function getShipAbleItemsAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $itemsToReturn = [];

        if (!$this->getProcessableHelper()->isProcessable($order)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'The current order state does not allow to create shipments']);
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

                $itemsToReturn[] = $event->getArguments();
            }
        }

        return $this->viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createShipmentAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');

        $form = $this->container->get('form.factory')->createNamed('', OrderShipmentCreationType::class);

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

            $order = $this->getOrderRepository()->find($resource['id']);

            if (!$order instanceof OrderInterface) {
                return $this->viewHandler->handle(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
            }

            try {
                // request shipment ready state from order, if it's our first shipment.
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

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    public function updateStateAction(Request $request): JsonResponse
    {
        $shipment = $this->getOrderShipmentRepository()->find($this->getParameterFromRequest($request, 'id'));
        $transition = $this->getParameterFromRequest($request, 'transition');

        if (!$shipment instanceof OrderShipmentInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'invalid shipment']);
        }

        //apply state machine
        $workflow = $this->getStateMachineManager()->get($shipment, 'coreshop_shipment');
        if (!$workflow->can($shipment, $transition)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($shipment, $transition);

        return $this->viewHandler->handle(['success' => true]);
    }

    public function renderAction(Request $request): Response
    {
        $shipmentId = (int) $this->getParameterFromRequest($request, 'id');
        $shipment = $this->getOrderShipmentRepository()->find($shipmentId);

        if ($shipment instanceof OrderShipmentInterface) {
            try {
                $responseData = $this->getOrderDocumentRenderer()->renderDocumentPdf($shipment);
                $header = [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="shipment-' . $shipment->getId() . '.pdf"',
                ];
            } catch (\Exception $e) {
                $responseData = '<strong>' . $e->getMessage() . '</strong><br>trace: ' . $e->getTraceAsString();
                $header = ['Content-Type' => 'text/html'];
            }

            return new Response($responseData, 200, $header);
        }

        throw new NotFoundHttpException(sprintf('Invoice with Id %s not found', $shipmentId));
    }

    protected function getOrderDocumentRenderer(): OrderDocumentRendererInterface
    {
        return $this->container->get('coreshop.renderer.order.pdf');
    }

    protected function getOrderShipmentRepository(): OrderShipmentRepositoryInterface
    {
        return $this->container->get('coreshop.repository.order_shipment');
    }

    protected function getProcessableHelper(): ProcessableInterface
    {
        return $this->container->get('coreshop.order.shipment.processable');
    }

    protected function getOrderRepository(): PimcoreRepositoryInterface
    {
        return $this->container->get('coreshop.repository.order');
    }

    protected function getShipmentFactory(): FactoryInterface
    {
        return $this->container->get('coreshop.factory.order_shipment');
    }

    protected function getOrderToShipmentTransformer(): OrderDocumentTransformerInterface
    {
        return $this->container->get(OrderToShipmentTransformer::class);
    }

    protected function getStateMachineManager(): StateMachineManager
    {
        return $this->container->get('coreshop.state_machine_manager');
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
                new SubscribedService('coreshop.state_machine_manager', StateMachineManagerInterface::class),
                new SubscribedService('coreshop.repository.order', OrderRepositoryInterface::class),
                new SubscribedService('coreshop.renderer.order.pdf', OrderDocumentRendererInterface::class, attributes: new Autowire(service: 'coreshop.renderer.order.pdf')),
                new SubscribedService('coreshop.repository.order_shipment', OrderShipmentRepositoryInterface::class, attributes: new Autowire(service:'coreshop.repository.order_shipment')),
                new SubscribedService('coreshop.order.shipment.processable', ProcessableInterface::class, attributes: new Autowire(service: 'coreshop.order.shipment.processable')),
                new SubscribedService('coreshop.factory.order_shipment', FactoryInterface::class, attributes: new Autowire(service: 'coreshop.factory.order_shipment')),
                new SubscribedService('event_dispatcher', EventDispatcherInterface::class),
                new SubscribedService(OrderToShipmentTransformer::class, OrderToShipmentTransformer::class),
                new SubscribedService(ErrorSerializer::class, ErrorSerializer::class),
            ]);
    }
}
