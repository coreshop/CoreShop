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
use CoreShop\Model\Currency;


use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_HelperController extends Admin
{
    public function getOrderAction() {
        $orderNumber = $this->getParam("orderNumber");

        if($orderNumber) {
            $list = new \Pimcore\Model\Object\CoreShopOrder\Listing();
            $list->setCondition("orderNumber = ? OR orderNumber = ?", array($orderNumber, \CoreShop\Model\Order::getValidOrderNumber($orderNumber)));

            $orders = $list->getObjects();

            if(count($orders) > 0) {
                $this->_helper->json(array("success" => true, "id" => $orders[0]->getId()));
            }
        }

        $this->_helper->json(array("success" => false));
    }

}