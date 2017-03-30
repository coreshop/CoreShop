<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderShipmentController
 *
 * @Route("/order-shipment")
 */
class OrderShipmentController extends Admin\AdminController
{
    public function getShipAbleItemsAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => 'Order with ID "'.$orderId.'" not found']);
        }

        $items = [];
        $itemsToReturn = [];

        if (!$order->hasPayments()) {
            return $this->json(['success' => false, 'message' => 'Can\'t create Shipment without valid order payment']);
        }

        try {
            $items = $order->getShipAbleItems();
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }

        foreach ($items as $item) {
            $orderItem = $item['item'];
            if ($orderItem instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Item) {
                $itemsToReturn[] = [
                    "orderItemId" => $orderItem->getId(),
                    "price" => $orderItem->getPrice(),
                    "maxToShip" => $item['amount'],
                    "amount" => $orderItem->getAmount(),
                    "amountShipped" => $orderItem->getAmount() - $item['amount'],
                    "toShip" => $item['amount'],
                    "tax" => $orderItem->getTotalTax(),
                    "total" => $orderItem->getTotal(),
                    "name" => $orderItem->getProduct() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product ? $orderItem->getProduct()->getName() : ""
                ];
            }
        }

        return $this->json(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createShipmentAction(Request $request)
    {
        $items = $request->get("items");
        $orderId = $request->get("id");
        $carrierId = $request->get("carrier");
        $trackingCode = $request->get("trackingCode");

        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);
        $carrier = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier::getById($carrierId);

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        if (!$carrier instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier) {
            return $this->json(['success' => false, 'message' => "Carrier with ID '$carrierId' not found"]);
        }

        try {
            $items = \Zend_Json::decode($items);

            $shipment = $order->createShipment($items, $carrier, $trackingCode);

            return $this->json(["success" => true, "shipmentId" => $shipment->getId()]);
        } catch (\CoreShop\Bundle\CoreShopLegacyBundle\Exception $ex) {
            return $this->json(['success' => false, 'message' => $ex->getMessage()]);
        }
    }

    public function changeTrackingCodeAction(Request $request)
    {
        $shipmentId = $request->get('shipmentId');
        $trackingCode = $request->get("trackingCode");

        $shipment = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment::getById($shipmentId);

        if (!$shipment instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment) {
            return $this->json(['success' => false, 'message' => "Shipment with ID '$shipmentId' not found"]);
        }

        if (!$trackingCode || $shipment->getTrackingCode() === $trackingCode) {
            return $this->json(['success' => false, 'message' => 'Tracking code did not change or is empty']);
        }

        $shipment->setTrackingCode($trackingCode);
        $shipment->save();

        return $this->json(['success' => true]);
    }

    public function renderShipmentAction(Request $request)
    {
        $shipmentId = $request->get('id');
        $shipment = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment::getById($shipmentId);

        if ($shipment instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment) {
            header('Content-type: application/pdf');
            header(sprintf('Content-Disposition: inline; filename="shipment-%s"', $shipment->getShipmentNumber()));
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');

            echo $shipment->generate()->getData();
        } else {
            echo "Shipment not found";
        }

        exit;
    }
}
