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
 * Class OrderInvoiceController
 *
 * @Route("/order-invoice")
 */
class OrderInvoiceController extends Admin\AdminController
{
    public function getInvoiceAbleItemsAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => 'Order with ID "'.$orderId.'" not found']);
        }

        $items = [];
        $itemsToReturn = [];

        if (!$order->hasPayments()) {
            return $this->json(['success' => false, 'message' => 'Can\'t create Invoice without valid order payment']);
        }

        try {
            $items = $order->getInvoiceAbleItems();
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }

        foreach ($items as $item) {
            $orderItem = $item['item'];
            if ($orderItem instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Item) {
                $itemsToReturn[] = [
                    "orderItemId" => $orderItem->getId(),
                    "price" => $orderItem->getPrice(),
                    "maxToInvoice" => $item['amount'],
                    "amount" => $orderItem->getAmount(),
                    "amountInvoiced" => $orderItem->getAmount() - $item['amount'],
                    "toInvoice" => $item['amount'],
                    "tax" => $orderItem->getTotalTax(),
                    "total" => $orderItem->getTotal(),
                    "name" => $orderItem->getProduct() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product ? $orderItem->getProduct()->getName() : ""
                ];
            }
        }

        return $this->json(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createInvoiceAction(Request $request)
    {
        $items = $request->get("items");
        $orderId = $request->get("id");
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        try {
            $items = \Zend_Json::decode($items);

            $invoice = $order->createInvoice($items);

            return $this->json(["success" => true, "invoiceId" => $invoice->getId()]);
        } catch (\CoreShop\Bundle\CoreShopLegacyBundle\Exception $ex) {
            return $this->json(['success' => false, 'message' => $ex->getMessage()]);
        }
    }

    public function renderInvoiceAction(Request $request)
    {
        $invoiceId = $request->get('id');
        $invoice = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice::getById($invoiceId);

        if ($invoice instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice) {
            header('Content-type: application/pdf');
            header(sprintf('Content-Disposition: inline; filename="invoice-%s"', \Pimcore\File::getValidFilename($invoice->getInvoiceNumber())));
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');

            echo $invoice->generate()->getData();
        } else {
            echo "Invoice not found";
        }

        exit;
    }
}
