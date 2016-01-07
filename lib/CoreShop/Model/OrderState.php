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

use CoreShop\Config;
use CoreShop\Plugin;
use CoreShop\Tool;
use Pimcore\Mail;
use Pimcore\Model\Document;

class OrderState extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $accepted;

    /**
     * @var boolean
     */
    public $shipped;

    /**
     * @var boolean
     */
    public $paid;

    /**
     * @var boolean
     */
    public $invoice;

    /**
     * @var boolean
     */
    public $email;

    /**
     * @var string
     */
    public $emailDocument;

    /**
     * Save OrderState
     *
     * @return mixed
     */
    public function save() {
        return $this->getDao()->save();
    }

    /**
     * get OrderState by ID
     *
     * @param $id
     * @return OrderState|null
     */
    public static function getById($id) {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    /**
     * Get all OrderState
     *
     * @return OrderState[]
     */
    public static function getOrderStates()
    {
        $list = new OrderState\Listing();

        return $list->getData();
    }

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
        if($this->getAccepted())
        {

        }

        if($this->getShipped())
        {

        }

        if($this->getInvoice())
        {
            if((bool)Config::getValue("INVOICE.CREATE")) {
                Invoice::generateInvoice($order);
            }
        }

        if($this->getPaid())
        {
            Plugin::actionHook("paymentConfirmation", array("order" => $order));
        }

        Plugin::actionHook("orderStatusUpdate", array("newOrderStatus" => $this, "order" => $order));

        if($this->getEmail())
        {
            $emailDocument = $this->getEmailDocument();
            if($emailDocument instanceof Document\Email) {
                $emailParameters = array(
                    "order" => $order,
                    "newOrderStatus" => $this,
                    "user" => $order->getCustomer()
                );

                $mail = new Mail();
                $mail->setDocument($emailDocument);
                $mail->setParams($emailParameters);
                $mail->addTo($order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname() . " " . $order->getCustomer()->getLastname());

                Tool::addAdminToMail($mail);

                $mail->send();
            }
        }

        $order->setOrderState($this);
        $order->save();

        return true;
        //TODO: Stock Management
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * @param boolean $accepted
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;
    }

    /**
     * @return boolean
     */
    public function getShipped()
    {
        return $this->shipped;
    }

    /**
     * @param boolean $shipped
     */
    public function setShipped($shipped)
    {
        $this->shipped = $shipped;
    }

    /**
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param boolean $paid
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    }

    /**
     * @return boolean
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param boolean $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return boolean
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param boolean $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmailDocument()
    {
        return $this->emailDocument;
    }

    /**
     * @param string $emailDocument
     */
    public function setEmailDocument($emailDocument)
    {
        $this->emailDocument = $emailDocument;
    }
}