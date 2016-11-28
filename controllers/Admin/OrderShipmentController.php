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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_OrderShipmentController
 */
class CoreShop_Admin_OrderShipmentController extends Admin
{
    public function getShipAbleItemsAction() {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        $items = $order->getShipAbleItems();
        $itemsToReturn = [];

        foreach($items as $item) {
            $orderItem = $item['item'];
            if($orderItem instanceof \CoreShop\Model\Order\Item) {
                $itemsToReturn[] = [
                    "orderItemId" => $orderItem->getId(),
                    "price" => $orderItem->getPrice(),
                    "maxToShip" => $item['amount'],
                    "amount" => $orderItem->getAmount(),
                    "amountShipped" => $orderItem->getAmount() - $item['amount'],
                    "toShip" => $item['amount'],
                    "tax" => $orderItem->getTotalTax(),
                    "total" => $orderItem->getTotal(),
                    "name" => $orderItem->getProduct() instanceof \CoreShop\Model\Product ? $orderItem->getProduct()->getName() : ""
                ];
            }
        }

        $this->_helper->json(array('success' => true, 'items' => $itemsToReturn));
    }

    public function createShipmentAction() {
        $items = $this->getParam("items");
        $orderId = $this->getParam("id");
        $carrierId = $this->getParam("carrier");
        $trackingCode = $this->getParam("trackingCode");

        $order = \CoreShop\Model\Order::getById($orderId);
        $carrier = \CoreShop\Model\Carrier::getById($carrierId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        if (!$carrier instanceof \CoreShop\Model\Carrier) {
            $this->_helper->json(array('success' => false, 'message' => "Carrier with ID '$carrierId' not found"));
        }

        try {
            $items = \Zend_Json::decode($items);

            $shipment = $order->createShipment($items, $carrier, $trackingCode);

            $this->_helper->json(["success" => true, "shipmentId" => $shipment->getId()]);
        }
        catch(\CoreShop\Exception $ex) {
            $this->_helper->json(array('success' => false, 'message' => $ex->getMessage()));
        }
    }

    public function changeTrackingCodeAction()
    {
        $shipmentId = $this->getParam('shipmentId');
        $trackingCode = $this->getParam("trackingCode");

        $shipment = \CoreShop\Model\Order\Shipment::getById($shipmentId);

        if (!$shipment instanceof \CoreShop\Model\Order\Shipment) {
            $this->_helper->json(array('success' => false, 'message' => "Shipment with ID '$shipmentId' not found"));
        }

        if (!$trackingCode || $shipment->getTrackingCode() === $trackingCode) {
            $this->_helper->json(array('success' => false, 'message' => "Tracking code did not change or is empty"));
        }

        $shipment->setTrackingCode($trackingCode);
        $shipment->save();

        $this->_helper->json(array('success' => true));
    }

    public function renderShipmentAction() {
        $shipmentId = $this->getParam('id');
        $shipment = \CoreShop\Model\Order\Shipment::getById($shipmentId);

        if($shipment instanceof \CoreShop\Model\Order\Shipment) {
            header('Content-type: application/pdf');
            header(sprintf('Content-Disposition: inline; filename="shipment-%s"', $shipment->getShipmentNumber()));
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');

            echo $shipment->generate()->getData();
        }
        else {
            echo "Shipment not found";
        }

        exit;
    }
}
