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

use CoreShop\Exception\UnsupportedException;
use CoreShop\Plugin;
use CoreShop\Tool;

use Pimcore\Model\Document;
use Pimcore\Mail;

class OrderState extends Base
{
    /**
     * Process OrderState for Order
     *
     * @param Order $order
     * @param null $locale
     * @return bool
     * @throws \Exception
     */
    public function processStep(Order $order, $locale = null)
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

    /**
     * returns discount for order
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return Document\Email
     */
    public function getEmailDocument($locale = null) {
        throw new UnsupportedException("getEmailDocument is not supported for " . get_class($this));
    }

    /**
     * returns accepted
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return boolean
     */
    public function getAccepted() {
        throw new UnsupportedException("getAccepted is not supported for " . get_class($this));
    }

    /**
     * returns shipped
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return boolean
     */
    public function getShipped() {
        throw new UnsupportedException("getShipped is not supported for " . get_class($this));
    }

    /**
     * returns paid
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return boolean
     */
    public function getPaid() {
        throw new UnsupportedException("getPaid is not supported for " . get_class($this));
    }

    /**
     * returns email
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return boolean
     */
    public function getEmail() {
        throw new UnsupportedException("getEmail is not supported for " . get_class($this));
    }

}