<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model;
use Pimcore\Controller\Action\Admin;
use CoreShop\Helper\ReportQuery;

class CoreShop_Admin_ReportsController extends Admin
{
    public function getProductsReportAction() {
        $filter = ReportQuery::extractFilterDefinition($this->getParam("filters"));

        $ordersItemsTable = "object_" . \Pimcore\Model\Object\CoreShopOrderItem::classId();

        $sql = "SELECT product__id as id, count(*) as count FROM $ordersItemsTable";

        if($filter) {
            $sql .= " WHERE $filter";
        }

        $sql .= " GROUP BY product__id ORDER BY count(*) DESC";

        $db = \Pimcore\Db::get();

        $productsQuery = $db->fetchAll($sql);
        $products = array();

        foreach($productsQuery as $pr) {
            $product = Model\Product::getById($pr['id']);

            $products[] = array(
                "id" => $product->getId(),
                "name" => $product->getName(),
                "count" => $pr['count']
            );
        }

        $this->_helper->json(array("data" => $products));
    }

    /**
     * Return Orders/Carts from last 31 Days
     */
    public function getOrdersCartsReportAction()
    {
        $filters = $this->getParam("filters", array("from" => date('01-m-Y'), "to" => date('m-t-Y')));
        $from = new \Pimcore\Date($filters['from']);
        $to = new \Pimcore\Date($filters['to']);

        $diff = $to->sub($from)->toValue();
        $days = ceil($diff / 60 / 60 / 24) +1;

        $startDate = $from->getTimestamp();

        $data = array();

        for ($i=0; $i<$days; $i++) {
            // documents
            $end = $startDate + ($i * 86400);
            $start = $end - 86399;
            $date = new \Zend_Date($start);

            $listOrders = new \Pimcore\Model\Object\CoreShopOrder\Listing();
            $listOrders->setCondition("o_creationDate > ? AND o_creationDate < ?", array($start, $end));

            $listCarts = new \Pimcore\Model\Object\CoreShopCart\Listing();
            $listCarts->setCondition("o_creationDate > ? AND o_creationDate < ?", array($start, $end));


            $data[] = array(
                "timestamp" => $start,
                "datetext" => $date->get(\Zend_Date::DATE_LONG),
                "orders" => count($listOrders->load()),
                "carts" => count($listCarts->load())
            );
        }

        $this->_helper->json(array("data" => $data));
    }

    /**
     * Return Sales from last 31 days
     */
    public function getSalesReportAction()
    {
        $filters = $this->getParam("filters", array("from" => date('01-m-Y'), "to" => date('m-t-Y')));
        $from = new \Pimcore\Date($filters['from']);
        $to = new \Pimcore\Date($filters['to']);

        $diff = $to->sub($from)->toValue();
        $days = ceil($diff / 60 / 60 / 24) +1;

        $startDate = $from->getTimestamp();

        $data = array();

        for ($i=0; $i<$days; $i++) {
            // documents
            $end = $startDate + ($i * 86400);
            $start = $end - 86399;
            $date = new \Zend_Date($start);

            $listOrders = new \Pimcore\Model\Object\CoreShopOrder\Listing();
            $listOrders->setCondition("o_creationDate > ? AND o_creationDate < ?", array($start, $end));
            $total = 0;

            foreach ($listOrders->getObjects() as $order) {
                $total += $order->getTotal();
            }

            $data[] = array(
                "timestamp" => $start,
                "datetext" => $date->get(\Zend_Date::DATE_LONG),
                "sales" => $total,
                "salesFormatted" => Tool::formatPrice($total)
            );
        }

        $this->_helper->json(array("data" => $data));
    }
}