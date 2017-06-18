<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\OrderShipmentCreationType;
use CoreShop\Bundle\OrderBundle\Transformer\OrderToShipmentTransformer;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderShipmentController extends AdminController
{
    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getShipAbleItemsAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->json(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $itemsToReturn = [];

        if (count($order->getPayments()) === 0) {
            return $this->json(['success' => false, 'message' => 'Can\'t create Shipment without valid order payment']);
        }

        try {
            $items = $this->getProcessableHelper()->getProcessableItems($order);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }

        foreach ($items as $item) {
            $orderItem = $item['item'];
            if ($orderItem instanceof OrderItemInterface) {
                $itemsToReturn[] = [
                    'orderItemId' => $orderItem->getId(),
                    'price' => $orderItem->getItemPrice(),
                    'maxToShip' => $item['quantity'],
                    'quantity' => $orderItem->getQuantity(),
                    'quantityShipped' => $orderItem->getQuantity() - $item['quantity'],
                    'toShip' => $item['quantity'],
                    'tax' => $orderItem->getTotalTax(),
                    'total' => $orderItem->getTotal(),
                    'name' => $orderItem->getProduct() instanceof PurchasableInterface ? $orderItem->getProduct()->getName() : '',
                ];
            }
        }

        return $this->json(['success' => true, 'items' => $itemsToReturn]);
    }

    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function createShipmentAction(Request $request)
    {
        $orderId = $request->get('id');

        $form = $this->get('form.factory')->createNamed('', OrderShipmentCreationType::class);

        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST'], true) && $handledForm->isValid()) {
            $resource = $handledForm->getData();

            $order = $this->getOrderRepository()->find($resource['id']);

            if (!$order instanceof OrderInterface) {
                return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
            }

            try {
                $items = $resource['items'];

                /**
                 * @var OrderShipmentInterface
                 */
                $shipment = $this->getShipmentFactory()->createNew();

                foreach ($resource as $key => $value) {
                    if ($key === 'items' || $key === 'id') continue;

                    $shipment->setValue($key, $value);
                }

                $shipment = $this->getOrderToShipmentTransformer()->transform($order, $shipment, $items);

                return $this->json(['success' => true, 'shipmentId' => $shipment->getId()]);
            } catch (\Exception $ex) {
                return $this->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        return $this->json(['success' => false, 'message' => "Method not supported, use POST"]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request)
    {
        $invoiceId = $request->get('id');
        $invoice = $this->getOrderShipmentRepository()->find($invoiceId);

        if ($invoice instanceof OrderShipmentInterface) {
            return new Response(
                $this->getOrderDocumentRenderer()->renderDocumentPdf($invoice),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="invoice-' . $invoice->getId() . '.pdf"',
                ]
            );
        }

        throw new NotFoundHttpException(sprintf('Invoice with Id %s not found', $invoiceId));
    }

    /**
     * @return OrderDocumentRendererInterface
     */
    protected function getOrderDocumentRenderer()
    {
        return $this->get('coreshop.renderer.order.pdf');
    }

    /**
     * @return PimcoreRepositoryInterface
     */
    protected function getOrderShipmentRepository()
    {
        return $this->get('coreshop.repository.order_shipment');
    }

    /**
     * @return ProcessableInterface
     */
    protected function getProcessableHelper()
    {
        return $this->get('coreshop.order.shipment.processable');
    }

    /**
     * @return PimcoreRepositoryInterface
     */
    protected function getOrderRepository()
    {
        return $this->get('coreshop.repository.order');
    }

    /**
     * @return FactoryInterface
     */
    protected function getShipmentFactory()
    {
        return $this->get('coreshop.factory.order_shipment');
    }

    /**
     * @return OrderToShipmentTransformer
     */
    protected function getOrderToShipmentTransformer()
    {
        return $this->get('coreshop.order.transformer.order_to_shipment');
    }
}
