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
use Pimcore\Model\Element\Note;
use Pimcore\Tool\Authentication;

class OrderState extends AbstractModel
{
    protected $localizedValues = array("emailDocument", "name");

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
    public static function getById($id)
    {
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
     * @return bool
     * @throws \Exception
     */
    public function processStep(Order $order)
    {
        $previousState = $order->getOrderState();
        //Check if new OrderState is the same as the current one
        if ($order->getOrderState() instanceof OrderState) {
            if ($order->getOrderState()->getId() === $this->getId()) {
                return false;
            }
        }

        if ($this->getAccepted()) {
        }

        if ($this->getShipped()) {
        }

        if ($this->getPaid()) {
            //Plugin::actionHook("paymentConfirmation", array("order" => $order));
        }

        if ($this->getInvoice()) {
            if ((bool)Configuration::get("SYSTEM.INVOICE.CREATE")) {
                //Generates the invoice, force re-generation cause of state change
                $order->getInvoice(true);
            }
        }


        if ($this->getEmail()) {
            $emailDocument = $this->getEmailDocument();
            $emailDocument = Document::getByPath($emailDocument);

            if ($emailDocument instanceof Document\Email) {
                $emailParameters = array_merge($order->getObjectVars(), $this->getObjectVars(), $order->getCustomer()->getObjectVars());
                $emailParameters['orderTotal'] = Tool::formatPrice($order->getTotal());
                $emailParameters['order'] = $order;
                
                unset($emailParameters['____pimcore_cache_item__']);

                $mail = new Mail();
                $mail->setDocument($emailDocument);
                $mail->setParams($emailParameters);
                $mail->setEnableLayoutOnPlaceholderRendering(false);
                $mail->addTo($order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname() . " " . $order->getCustomer()->getLastname());

                if ((bool)Configuration::get("SYSTEM.INVOICE.CREATE")) {
                    if ($this->getInvoice()) {
                        $invoice = $order->getInvoice();

                        if ($invoice instanceof \Pimcore\Model\Asset\Document) {
                            $attachment = new \Zend_Mime_Part($invoice->getData());
                            $attachment->type = $invoice->getMimetype();
                            $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                            $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                            $attachment->filename = $invoice->getFilename();

                            $mail->addAttachment($attachment);
                        }
                    }
                }

                //check if admin copy mail address has been set. if => send him a lovely copy!
                $sendBccToUser = Configuration::get("SYSTEM.MAIL.ORDER.BCC");
                $adminMailAddress = Configuration::get("SYSTEM.MAIL.ORDER.NOTIFICATION");

                if ($sendBccToUser === true && !empty($adminMailAddress)) {
                    $mail->addBcc(explode(',', $adminMailAddress));
                }

                $mail->send();
            }
        }

        $order->setOrderState($this);
        $order->save();

        $translate = Plugin::getTranslate(\Pimcore\Tool::getDefaultLanguage())->getAdapter();

        $note = $order->createNote("coreshop-orderstate");
        $note->setTitle(sprintf($translate->translate("coreshop_note_orderstate_change"), $this->getName()));
        $note->setDescription(sprintf($translate->translate("coreshop_note_orderstate_change_description"), $this->getName()));

        if ($previousState instanceof OrderState) {
            $note->addData("fromState", "text", $previousState->getName());
        }

        $note->addData("toState", "text", $this->getName());
        $note->save();

        Plugin::actionHook("orderstate.process.post", array("newOrderStatus" => $this, "order" => $order));

        //@TODO: Stock Management

        return true;
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
     * @param string $language language
     * @return string
     */
    public function getName($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue("name", $language);
    }

    /**
     * @param string $name
     * @param string $language language
     */
    public function setName($name, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue("name", $name, $language);
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
