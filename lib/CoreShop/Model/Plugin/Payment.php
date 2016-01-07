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

use CoreShop\Config;
use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\OrderState;
use CoreShop\Plugin;

abstract class Payment implements AbstractPlugin
{
    public function getPaymentFee(Cart $cart) {
        throw new UnsupportedException("");
    }

    public function processPayment(Order $order) {
        throw new UnsupportedException("");
    }

    public function validateOrder(\CoreShop\Model\Payment $payment = null, Order $order = null, OrderState $orderState = null) {
        $paymentHandled = false;
        $result = Plugin::getEventManager()->trigger('payment', $this, array("payment" => $payment), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $paymentHandled = $result->last();
        }

        if(!$paymentHandled)
        {
            if($payment instanceof \CoreShop\Model\Payment) {
                if($order instanceof Order) {
                    if($orderState instanceof $orderState) {
                        $orderState->processStep($order);

                        $paymentHandled = true;
                    }
                }
            }
        }

        //Error occured, set state to error
        if(!$paymentHandled) {
            if($order instanceof Order) {
                $errorOrderState = OrderState::getById(Config::getValue("ORDERSTATE.ERROR"));
                $errorOrderState->processStep($order);

                $paymentHandled = true;
            }
        }

        if(!$paymentHandled) {
            //Something unkown happend
            die("something unkown happend");
        }
    }
}