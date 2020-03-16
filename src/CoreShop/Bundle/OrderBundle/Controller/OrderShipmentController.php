<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\OrderShipmentCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderShipmentController extends PimcoreController
{
    public function getShipAbleItemsAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        EventDispatcherInterface $eventDispatcher,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $orderId = $request->get('id');
        $order = $orderRepository->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $itemsToReturn = [];

        if (!$this->getProcessableHelper()->isProcessable($order)) {
            return $viewHandler->handle(['success' => false, 'message' => 'The current order state does not allow to create shipments']);
        }

        try {
            $items = $this->getProcessableHelper()->getProcessableItems($order);
        } catch (\Exception $e) {
            return $viewHandler->handle(['success' => false, 'message' => $e->getMessage()]);
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

                $eventDispatcher->dispatch('coreshop.order.shipment.prepare_ship_able', $event);

                $itemsToReturn[] = $event->getArguments();
            }
        }

        return $viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createShipmentAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        FormFactoryInterface $formFactory,
        ErrorSerializer $errorSerializer,
        StateMachineManager $stateMachineManager,
        FactoryInterface $orderShipmentFactory,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $orderId = $request->get('id');

        $form = $formFactory->createNamed('', OrderShipmentCreationType::class);

        $handledForm = $form->handleRequest($request);

        if ($request->getMethod() === 'POST') {
            if (!$handledForm->isValid()) {
                return $viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $errorSerializer->serializeErrorFromHandledForm($form),
                    ]
                );
            }

            $resource = $handledForm->getData();

            $order = $orderRepository->find($resource['id']);

            if (!$order instanceof OrderInterface) {
                return $viewHandler->handle(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
            }

            try {
                // request shipment ready state from order, if it's our first shipment.
                $workflow = $stateMachineManager->get($order, 'coreshop_order_shipment');
                if ($workflow->can($order, OrderShipmentTransitions::TRANSITION_REQUEST_SHIPMENT)) {
                    $workflow->apply($order, OrderShipmentTransitions::TRANSITION_REQUEST_SHIPMENT);
                }

                /**
                 * @var OrderShipmentInterface $shipment
                 */
                $shipment = $orderShipmentFactory->createNew();
                $shipment->setState(ShipmentStates::STATE_NEW);

                foreach ($resource as $key => $value) {
                    if (in_array($key, ['items', 'id', 'state'])) {
                        continue;
                    }

                    $shipment->setValue($key, $value);
                }

                $items = $resource['items'];
                $shipment = $this->getOrderToShipmentTransformer()->transform($order, $shipment, $items);

                return $viewHandler->handle(['success' => true, 'shipmentId' => $shipment->getId()]);
            } catch (\Exception $ex) {
                return $viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        return $viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    public function updateStateAction(
        Request $request,
        OrderShipmentRepositoryInterface $orderShipmentRepository,
        StateMachineManager $stateMachineManager,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $shipment = $orderShipmentRepository->find($request->get('id'));
        $transition = $request->get('transition');

        if (!$shipment instanceof OrderShipmentInterface) {
            return $viewHandler->handle(['success' => false, 'message' => 'invalid shipment']);
        }

        //apply state machine
        $workflow = $stateMachineManager->get($shipment, 'coreshop_shipment');
        if (!$workflow->can($shipment, $transition)) {
            return $viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($shipment, $transition);

        return $viewHandler->handle(['success' => true]);
    }

    public function renderAction(
        Request $request,
        OrderShipmentRepositoryInterface $orderShipmentRepository,
        OrderDocumentRendererInterface $orderDocumentRenderer
    ): Response
    {
        $shipmentId = $request->get('id');
        $shipment = $orderShipmentRepository->find($shipmentId);

        if ($shipment instanceof OrderShipmentInterface) {
            try {
                $responseData = $orderDocumentRenderer->renderDocumentPdf($shipment);
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

    /**
     * @return ProcessableInterface
     */
    protected function getProcessableHelper()
    {
        return $this->get('coreshop.order.shipment.processable');
    }

    /**
     * @return OrderDocumentTransformerInterface
     */
    protected function getOrderToShipmentTransformer()
    {
        return $this->get(OrderToShipmentTransformer::class);
    }
}
