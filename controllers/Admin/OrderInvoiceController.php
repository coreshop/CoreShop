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
use Pimcore\Model\Object;

/**
 * Class CoreShop_Admin_OrderInvoiceController
 */
class CoreShop_Admin_OrderInvoiceController extends Admin
{
    public function getInvoiceAbleItemsAction() {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        $items = $order->getInvoiceAbleItems();
        $itemsToReturn = [];

        foreach($items as $item) {
            $orderItem = $item['item'];
            if($orderItem instanceof \CoreShop\Model\Order\Item) {
                $itemsToReturn[] = [
                    "orderItemId" => $orderItem->getId(),
                    "price" => $orderItem->getPrice(),
                    "maxToInvoice" => $item['amount'],
                    "amount" => $orderItem->getAmount(),
                    "amountInvoiced" => $orderItem->getAmount() - $item['amount'],
                    "toInvoice" => $item['amount'],
                    "tax" => $orderItem->getTotalTax(),
                    "total" => $orderItem->getTotal(),
                    "name" => $orderItem->getProduct() instanceof \CoreShop\Model\Product ? $orderItem->getProduct()->getName() : ""
                ];
            }
        }

        $this->_helper->json(array('success' => true, 'items' => $itemsToReturn));
    }

    public function createInvoiceAction() {
        $items = $this->getParam("items");
        $orderId = $this->getParam("id");
        $order = \CoreShop\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        try {
            $items = \Zend_Json::decode($items);

            $invoice = $order->createInvoice($items);

            $this->_helper->json(["success" => true, "invoiceId" => $invoice->getId()]);
        }
        catch(\CoreShop\Exception $ex) {
            $this->_helper->json(array('success' => false, 'message' => $ex->getMessage()));
        }
    }

    public function renderInvoiceAction() {
        $invoiceId = $this->getParam('id');
        $invoice = \CoreShop\Model\Order\Invoice::getById($invoiceId);

        if($invoice instanceof \CoreShop\Model\Order\Invoice) {
            header('Content-type: application/pdf');
            header(sprintf('Content-Disposition: inline; filename="invoice-%s"', \Pimcore\File::getValidFilename($invoice->getInvoiceNumber())));
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');

            echo $invoice->generate()->getData();
        }
        else {
            echo "Invoice not found";
        }

        exit;
    }
}
