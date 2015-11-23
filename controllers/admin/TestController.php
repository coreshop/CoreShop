<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model;

use Pimcore\Model\Object\CoreShopOrder;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_TestController extends Admin
{
    public function testSelectObjectAction(){
        $orderId = 2082;

        $order = CoreShopOrder::getById($orderId);

        var_dump($order->getOrderState());

        exit;
    }
}