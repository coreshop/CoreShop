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
use CoreShop\Config;
use CoreShop\Tool;
use CoreShop\Helper\Country;

use CoreShop\Model;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_StatisticsController extends Admin
{
    public function init() {

        parent::init();

        // check permissions
        $notRestrictedActions = array();

        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_statistics");
        }
    }

    /**
     * Return Orders/Carts from last 31 Days
     */
    public function getOrdersCartsFromLastDaysAction()
    {
        $days = 31;
        $startDate = mktime(23,59,59,date("m"),date("d"),date("Y"));

        $data = array();

        for ($i=0; $i<$days; $i++) {
            // documents
            $end = $startDate - ($i*86400);
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

        $data = array_reverse($data);

        $this->_helper->json(array("data" => $data));
    }

    /**
     * Return Sales from last 31 days
     */
    public function getSalesFromLastDaysAction() {
        $days = 31;
        $startDate = mktime(23,59,59,date("m"),date("d"),date("Y"));

        $data = array();

        for ($i=0; $i < $days; $i++) {
            // documents
            $end = $startDate - ($i*86400);
            $start = $end - 86399;
            $date = new \Zend_Date($start);

            $listOrders = new \Pimcore\Model\Object\CoreShopOrder\Listing();
            $listOrders->setCondition("o_creationDate > ? AND o_creationDate < ?", array($start, $end));
            $total = 0;

            foreach($listOrders->getObjects() as $order) {
                $total += $order->getTotal();
            }

            $data[] = array(
                "timestamp" => $start,
                "datetext" => $date->get(\Zend_Date::DATE_LONG),
                "sales" => $total,
                "salesFormatted" => Tool::formatPrice($total)
            );
        }

        $data = array_reverse($data);

        $this->_helper->json(array("data" => $data));
    }
}