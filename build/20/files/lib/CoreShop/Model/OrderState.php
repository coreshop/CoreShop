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

namespace CoreShop\Model;

use CoreShop\Plugin;
use CoreShop\Tool;
use Pimcore\Mail;
use Pimcore\Model\Document;

class OrderState extends AbstractModel
{
    protected $localizedValues = array("emailDocument");

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
     * get OrderState by ID
     *
     * @param $id
     * @return OrderState|null
     */
    public static function getById($id) {
        return parent::getById($id);
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

        if($this->getPaid())
        {
            //Plugin::actionHook("paymentConfirmation", array("order" => $order));
        }

        if($this->getInvoice())
        {
            if((bool)Configuration::get("SYSTEM.INVOICE.CREATE")) {
                Invoice::generateInvoice($order);
            }
        }

        //Todo: remove?
        Plugin::actionHook("orderStatusUpdate", array("newOrderStatus" => $this, "order" => $order));

        if($this->getEmail())
        {
            $emailDocument = $this->getEmailDocument();
            $emailDocument = Document::getByPath($emailDocument);

            if($emailDocument instanceof Document\Email) {

                $emailParameters = array_merge($order->getObjectVars(), $this->getObjectVars(), $order->getCustomer()->getObjectVars());
                $emailParameters['orderTotal'] = $order->gettotal();

                unset($emailParameters['____pimcore_cache_item__']);

                $mail = new Mail();
                $mail->setDocument($emailDocument);
                $mail->setParams($emailParameters);
                $mail->setEnableLayoutOnPlaceholderRendering(false);
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
     * @param string $language language
     * @return string
     */
    public function getEmailDocument($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue("emailDocument", $language);
    }

    /**
     * @param string $emailDocument
     * @param string $language language
     */
    public function setEmailDocument($emailDocument, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue("emailDocument", $emailDocument, $language);
    }
}