<?php
/**
 * CoreShop.
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
namespace CoreShop\Model\Order;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use CoreShop\Plugin;
use CoreShop\Tool;
use Pimcore\Mail;
use Pimcore\Model\Document;

class State extends AbstractModel
{
    protected $localizedValues = array('emailDocument', 'name');

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $accepted;

    /**
     * @var bool
     */
    public $shipped;

    /**
     * @var bool
     */
    public $paid;

    /**
     * @var bool
     */
    public $invoice;

    /**
     * @var bool
     */
    public $email;

    /**
     * @var string
     */
    public $color;

    /**
     * Process OrderState for Order.
     *
     * @param Order $order
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function processStep(Order $order)
    {
        $previousState = $order->getOrderState();
        //Check if new OrderState is the same as the current one
        if ($order->getOrderState() instanceof self) {
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
            if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE')) {
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
                $mail->addTo($order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname().' '.$order->getCustomer()->getLastname());

                if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE')) {
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
                $sendBccToUser = Configuration::get('SYSTEM.MAIL.ORDER.BCC');
                $adminMailAddress = Configuration::get('SYSTEM.MAIL.ORDER.NOTIFICATION');

                if ($sendBccToUser === true && !empty($adminMailAddress)) {
                    $mail->addBcc(explode(',', $adminMailAddress));
                }

                $mail->send();
            }
        }

        $order->setOrderState($this);
        $order->save();

        $translate = Tool::getTranslate();
        $note = $order->createNote('coreshop-orderstate');
        $note->setTitle(sprintf($translate->translate('coreshop_note_orderstate_change'), $this->getName()));
        $note->setDescription(sprintf($translate->translate('coreshop_note_orderstate_change_description'), $this->getName()));

        if ($previousState instanceof self) {
            $note->addData('fromState', 'text', $previousState->getId());
        }
        
        $note->addData('toState', 'text', $this->getId());

        $note->save();

        Plugin::actionHook('orderstate.process.post', array('newOrderStatus' => $this, 'order' => $order));

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
     *
     * @return string
     */
    public function getName($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue('name', $language);
    }

    /**
     * @param string $name
     * @param string $language language
     */
    public function setName($name, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue('name', $name, $language);
    }

    /**
     * @return bool
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * @param bool $accepted
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;
    }

    /**
     * @return bool
     */
    public function getShipped()
    {
        return $this->shipped;
    }

    /**
     * @param bool $shipped
     */
    public function setShipped($shipped)
    {
        $this->shipped = $shipped;
    }

    /**
     * @return bool
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    }

    /**
     * @return bool
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param bool $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return bool
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param bool $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @param string $language language
     *
     * @return string
     */
    public function getEmailDocument($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue('emailDocument', $language);
    }

    /**
     * @param string $emailDocument
     * @param string $language      language
     */
    public function setEmailDocument($emailDocument, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue('emailDocument', $emailDocument, $language);
    }
}
