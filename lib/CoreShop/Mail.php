<?php

namespace CoreShop;

use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use Pimcore\Mail as PimcoreMail;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;

class Mail extends PimcoreMail {
    /**
     * Sends this email using the given transport or with the settings from "Settings" -> "System" -> "Email Settings"
     *
     * IMPORTANT: If the debug mode is enabled in "Settings" -> "System" -> "Debug" all emails will be sent to the
     * debug email addresses that are given in "Settings" -> "System" -> "Email Settings" -> "Debug email addresses"
     *
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param  \Zend_Mail_Transport_Abstract $transport
     * @return \Pimcore\Mail Provides fluent interface
     */
    public function send($transport = null)
    {
        $sendBccToUser = Configuration::get('SYSTEM.MAIL.ORDER.BCC');
        $adminMailAddress = Configuration::get('SYSTEM.MAIL.ORDER.NOTIFICATION');

        if ($sendBccToUser === true && !empty($adminMailAddress)) {
            $this->addBcc(explode(',', $adminMailAddress));
        }

        return parent::send($transport);
    }

    /**
     * Send email which belongs to an order
     *
     * @param $emailDocument
     * @param Order $order
     * @param Order\State $orderState
     * @throws Exception\UnsupportedException
     * @throws \Exception
     */
    public static function sendOrderMail($emailDocument, Order $order, Order\State $orderState = null) {
        if ($emailDocument instanceof Document\Email) {
            $emailParameters = array_merge($order->getObjectVars(), $orderState instanceof Order\State ? $orderState->getObjectVars() : [], $order->getCustomer()->getObjectVars());
            $emailParameters['orderTotal'] = Tool::formatPrice($order->getTotal());
            $emailParameters['order'] = $order;

            unset($emailParameters['____pimcore_cache_item__']);

            $mail = new self();
            $mail->setDocument($emailDocument);
            $mail->setParams($emailParameters);
            $mail->setEnableLayoutOnPlaceholderRendering(false);
            $mail->addTo($order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname().' '.$order->getCustomer()->getLastname());

            if($orderState instanceof Order\State) {
                if ((bool)Configuration::get('SYSTEM.INVOICE.CREATE')) {
                    if ($orderState->getInvoice()) {
                        $invoice = $order->getInvoice();

                        if ($invoice instanceof Asset\Document) {
                            $attachment = new \Zend_Mime_Part($invoice->getData());
                            $attachment->type = $invoice->getMimetype();
                            $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                            $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                            $attachment->filename = $invoice->getFilename();

                            $mail->addAttachment($attachment);
                        }
                    }
                }
            }

            $mail->send();
        }
    }
}