<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\OrderShipmentCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Order\Transformer\OrderToShipmentTransformer;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderShipmentController extends PimcoreController
{
    public function getShipAbleItemsAction(Request $request): JsonResponse
    {
        $orderId = $request->get('id');
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

                $this->get('event_dispatcher')->dispatch($event, 'coreshop.order.shipment.prepare_ship_able');

                $itemsToReturn[] = $event->getArguments();
            }
        }

        return $this->viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createShipmentAction(Request $request): JsonResponse
    {
        $orderId = $request->get('id');

        $form = $this->get('form.factory')->createNamed('', OrderShipmentCreationType::class);

        $handledForm = $form->handleRequest($request);

        if ('POST' === $request->getMethod()) {
            if (!$handledForm->isValid()) {
                return $this->viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $this->get(ErrorSerializer::class)->serializeErrorFromHandledForm($form),
                    ]
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
        $shipment = $this->getOrderShipmentRepository()->find($request->get('id'));
        $transition = $request->get('transition');

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
        $shipmentId = $request->get('id');
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
        return $this->get('coreshop.renderer.order.pdf');
    }

    protected function getOrderShipmentRepository(): OrderShipmentRepositoryInterface
    {
        return $this->get('coreshop.repository.order_shipment');
    }

    protected function getProcessableHelper(): ProcessableInterface
    {
        return $this->get('coreshop.order.shipment.processable');
    }

    protected function getOrderRepository(): PimcoreRepositoryInterface
    {
        return $this->get('coreshop.repository.order');
    }

    protected function getShipmentFactory(): FactoryInterface
    {
        return $this->get('coreshop.factory.order_shipment');
    }

    protected function getOrderToShipmentTransformer(): OrderDocumentTransformerInterface
    {
        return $this->get(OrderToShipmentTransformer::class);
    }

    protected function getStateMachineManager(): StateMachineManager
    {
        return $this->get('coreshop.state_machine_manager');
    }
}
