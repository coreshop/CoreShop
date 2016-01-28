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

namespace CoreShop\Model\Plugin;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Carrier;
use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\OrderState;
use CoreShop\Plugin;
use Pimcore\Date;
use Pimcore\Model\Object\CoreShopOrder;
use Pimcore\Model\Object\Service;

abstract class Payment implements AbstractPlugin
{
    /**
     * Check if available for cart
     *
     * @param Cart $cart
     * @returns boolean if available
     */
    public function isAvailable(Cart $cart) {
        return true;
    }

    /**
     * Get Payment Fee
     *
     * @param Cart $cart
     * @throws UnsupportedException
     */
    public function getPaymentFee(Cart $cart) {
        throw new UnsupportedException("");
    }

    /**
     * Process Payment
     *
     * @param Order $order
     * @throws UnsupportedException
     */
    public function process(Order $order) {
        throw new UnsupportedException("");
    }

    /**
     * Creates order after successfull payment
     *
     * @param Cart $cart
     * @param OrderState $state
     * @param $totalPayed
     * @return Order
     */
    public function createOrder(Cart $cart, OrderState $state, $totalPayed = 0, $language = null) {
        $orderNumber = Order::getNextOrderNumber();

        if(is_null($language)) {
            $language = \Zend_Registry::get("Zend_Locale");
        }

        $order = new CoreShopOrder();
        $order->setKey(\Pimcore\File::getValidFilename($orderNumber));
        $order->setOrderNumber($orderNumber);
        $order->setParent(Service::createFolderByPath('/coreshop/orders/' . date('Y/m/d')));
        $order->setPublished(true);
        $order->setLang($language);
        $order->setCustomer($cart->getUser());
        $order->setShippingAddress($cart->getShippingAddress());
        $order->setBillingAddress($cart->getBillingAddress());
        $order->setPaymentProviderToken($this->getIdentifier());
        $order->setPaymentProvider($this->getName());
        $order->setPaymentProviderDescription($this->getDescription());
        $order->setOrderDate(new Date());

        if($cart->getCarrier() instanceof Carrier) {
            $order->setCarrier($cart->getCarrier());
            $order->setShipping($cart->getCarrier()->getDeliveryPrice($cart));
        }
        else {
            $order->setShipping(0);
        }

        $order->setPaymentFee($cart->getPaymentFee());
        $order->setTotal($cart->getTotal());
        $order->setSubtotal($cart->getSubtotal());
        $order->save();
        $order->importCart($cart);

        $order->createPayment($this, $totalPayed, true);

        $state->processStep($order);

        Plugin::actionHook("order.created", array("order" => $order) );

        return $order;

    }

    /**
     * assemble route with zend router
     *
     * @param $module string module name
     * @action $action string action name
     * @return string
     */
    public function url($module, $action) {
        $controller = \Zend_Controller_Front::getInstance();
        $router = $controller->getRouter();

        return $router->assemble(array("module" => $module, "action" => $action, "lang" => (string)\Zend_Registry::get("Zend_Locale")), "coreshop_payment");
    }
}