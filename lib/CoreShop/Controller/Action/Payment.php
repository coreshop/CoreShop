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
        \CoreShop\Plugin::getEventManager()->trigger('payment.fail', $this);
    }

    protected function paymentSuccess(\Pimcore\Model\Object\CoreShopPayment $payment)
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

            if($order instanceof \Pimcore\Model\Object\CoreShopOrder)
            {
                $user = $order->getCustomer();

                if($user instanceof \CoreShop\Plugin\User)
                {
                    $view = new \Pimcore\View();

                    $view->setScriptPath(
                        $this->view->getScriptPaths()
                    );

                    $view->registerHelper(new \Pimcore\View\Helper\Url(), "url");
                    $view->language = $this->language;
                    $view->order = $order;
                    $view->user = $user;
                    $view->payment = $payment;

                    $mailDocumentPath = "/".$this->language."/shop/email/new-order";
                    $mailDocument = \Pimcore\Model\Document::getByPath($mailDocumentPath);

                    if($mailDocument instanceof \Pimcore\Model\Document\Email) {
                        $orderDetailsString = $view->render("coreshop/email/helper/order-details.php");
                        $shippingAddressString = $view->partial("coreshop/email/helper/address.php", array("address" => $user->findAddressByName($order->getShippingAddress())));
                        $billingAddressString =  $view->partial("coreshop/email/helper/address.php", array("address" => $user->findAddressByName($order->getBillingAddress())));

                        $params = array(
                            'gender' => $user->getGender(),
                            'firstname' => $user->getFirstname(),
                            'lastname' => $user->getLastname(),
                            'email' => $user->getEmail(),
                            'object' => $user,
                            'token' => $user->getProperty("token"),
                            'language' => $this->language,
                            'orderDetails' => $orderDetailsString,
                            'shippingAddress' => $shippingAddressString,
                            'billingAddress' => $billingAddressString
                        );

                        $mail = new Pimcore_Mail();
                        $mail->addTo($user->getEmail());
                        $mail->setDocument($mailDocument);
                        $mail->setParams($params);
                        $mail->send();
                    } else {
                        \Logger::error("Mail Document for new Order in $mailDocumentPath not found");
                    }
                }
            }
        }
    }
}
