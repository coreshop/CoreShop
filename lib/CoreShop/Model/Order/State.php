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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Order;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use CoreShop\Mail;
use Pimcore\Model\Document;

/**
 * Class State
 * @package CoreShop\Model\Order
 */
class State extends AbstractModel
{
    protected $localizedValues = array('emailDocument', 'name');

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
            if ((bool) Configuration::get('SYSTEM.SHIPMENT.CREATE')) {
                $shipments = $order->getShipments();

                if(count($shipments) === 0) {
                    $order->createShipmentForAllItems();
                }
            }
        }

        if ($this->getPaid()) {
            //\CoreShop::actionHook("paymentConfirmation", array("order" => $order));
        }

        if ($this->getInvoice()) {
            if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE')) {
                $invoices = $order->getInvoices();

                if(count($invoices) === 0) {
                    $order->createInvoiceForAllItems();
                }
            }
        }

        if ($this->getEmail()) {
            $emailDocument = $this->getEmailDocument();
            $emailDocument = Document::getByPath($emailDocument);

            Mail::sendOrderMail($emailDocument, $order, $order->getOrderState());
        }

        $order->setOrderState($this);
        $order->save();

        $translate = \CoreShop::getTools()->getTranslate();
        $note = $order->createNote('coreshop-orderstate');
        $note->setTitle(sprintf($translate->translate('coreshop_note_orderstate_change'), $this->getName()));
        $note->setDescription(sprintf($translate->translate('coreshop_note_orderstate_change_description'), $this->getName()));

        if ($previousState instanceof self) {
            $note->addData('fromState', 'text', $previousState->getId());
        }
        
        $note->addData('toState', 'text', $this->getId());

        $note->save();

        \CoreShop::actionHook('orderstate.process.post', array('newOrderStatus' => $this, 'order' => $order));

        //@TODO: Stock Management

        return true;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
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
