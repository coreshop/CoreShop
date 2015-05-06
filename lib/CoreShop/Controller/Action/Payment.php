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

namespace CoreShop\Controller\Action;

use CoreShop\Controller\Action;

use CoreShop\Plugin;

use Pimcore\Model\Object\CoreShopPayment;
use Pimcore\Model\Object\CoreShopOrder;
use Pimcore\Model\Object\CoreShopUser;
use Pimcore\Model\Object\CoreShopOrderState;

class Payment extends Action {
    
    protected function paymentReturnAction () {
        $this->prepareCart();
        $this->cart->delete();
        
        unset($this->session->order);
        unset($this->session->cart);
        unset($this->session->cartId);
    }

    protected function paymentFail()
    {
        Plugin::getEventManager()->trigger('payment.fail', $this);
    }

    protected function paymentSuccess(CoreShopPayment $payment)
    {
        $paymentSuccessHandled = false;
        $result = \CoreShop\Plugin::getEventManager()->trigger('payment.success', $this, array("payment" => $payment, "language" => $this->language), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $paymentSuccessHandled = $result->last();
        }

        if(!$paymentSuccessHandled) {
            $order = $payment->getOrder();

            if($order instanceof CoreShopOrder)
            {
                $statePaied = CoreShopOrderState::getByPath("/coreshop/order-states/02-payment-received");//TODO: Make Order State per Type Configurable
                $statePaied->processStep($order);
            }
        }
    }
}
