<?php

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo: maybe we should move this one to the AdminBundle?
 */
class OrderShipmentController extends AdminController
{
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
                    "orderItemId" => $orderItem->getId(),
                    "price" => $orderItem->getItemPrice(),
                    "maxToShip" => $item['quantity'],
                    "quantity" => $orderItem->getQuantity(),
                    "quantityShipped" => $orderItem->getQuantity() - $item['quantity'],
                    "toShip" => $item['quantity'],
                    "tax" => $orderItem->getTotalTax(),
                    "total" => $orderItem->getTotal(),
                    "name" => $orderItem->getProduct() instanceof ProductInterface ? $orderItem->getProduct()->getName() : ""
                ];
            }
        }

        return $this->json(['success' => true, 'items' => $itemsToReturn]);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function createShipmentAction(Request $request)
    {
        $items = $request->get("items");
        $orderId = $request->get("id");
        $order = $this->getOrderRepository()->find($orderId);
        $carrierId = $request->get("carrier");
        $trackingCode = $request->get("trackingCode");
        $carrier = $this->get('coreshop.repository.carrier')->find($carrierId);

        if (!$order instanceof OrderInterface) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        if (!$carrier instanceof CarrierInterface) {
            return $this->json(['success' => false, 'message' => "Carrier with ID '$carrierId' not found"]);
        }

        try {
            $items = $this->decodeJson($items);

            /**
             * @var $shipment OrderShipmentInterface
             */
            $shipment = $this->getShipmentFactory()->createNew();
            $shipment->setCarrier($carrier);
            $shipment->setTrackingCode($trackingCode);

            $shipment = $this->getOrderToShipmentTransformer()->transform($order, $shipment, $items);

            return $this->json(["success" => true, "shipmentId" => $shipment->getId()]);
        } catch (\Exception $ex) {
            return $this->json(['success' => false, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * @return ProcessableInterface
     */
    private function getProcessableHelper()
    {
        return $this->get('coreshop.order.shipment.processable');
    }

    /**
     * @return PimcoreRepositoryInterface
     */
    private function getOrderRepository()
    {
        return $this->get('coreshop.repository.order');
    }

    private function getShipmentFactory()
    {
        return $this->get('coreshop.factory.order_shipment');
    }

    private function getOrderToShipmentTransformer()
    {
        return $this->get('coreshop.order.transformer.order_to_shipment');
    }
}