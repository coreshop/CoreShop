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

namespace CoreShop\Model;

use CoreShop\Plugin;
use CoreShop\Tool;

use Pimcore\Model\Document;
use Pimcore\Model\Object\CoreShopOrder;
use Pimcore\Mail;

use Pimcore\View;
use Pimcore\View\Helper\Url;

class OrderState extends Base {
    public function processStep(CoreShopOrder $order, $locale = null)
    {
        $emailDocument = $this->getEmailDocument($locale);
        $emailParameters = array(
            "order" => $order,
            "newOrderStatus" => $this,
            "user" => $order->getCustomer()
        );

        if($this->getAccepted()) {

        }

        if($this->getShipped()) {

        }

        if($this->getPaid()) {
            Plugin::actionHook("paymentConfirmation", array("order" => $order));
        }

        Plugin::actionHook("orderStatusUpdate", array("newOrderStatus" => $this, "order" => $order));

        if($this->getEmail() && $emailDocument instanceof Document\Email) {
            $mail = new Mail();
            $mail->setDocument($emailDocument);
            $mail->setParams($emailParameters);
            $mail->addTo($order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname() . " " . $order->getCustomer()->getLastname());

            Tool::addAdminToMail($mail);

            $mail->send();
        }

        $order->setOrderState($this);
        $order->save();

        return true;
        //TODO: Stock Management
    }
}