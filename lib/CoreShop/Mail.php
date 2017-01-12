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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use CoreShop\Model\Configuration;
use CoreShop\Model\Messaging\Message;
use CoreShop\Model\Order;
use CoreShop\Model\Shop;
use Pimcore\Mail as PimcoreMail;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Pimcore\Model\Tool\Email\Log;

/**
 * Class Mail
 * @package CoreShop
 */
class Mail extends PimcoreMail
{
    /**
     * @param $emailDocument
     * @param \Pimcore\Model\AbstractModel $model
     * @param $recipient
     * @param array $params
     *
     * @return bool
     */
    public static function sendMail($emailDocument, $model, $recipient, $params = [])
    {
        //init Template
        //TODO: Shop init?
        //\CoreShop::getTools()->initTemplateForShop($shop);

        $mail = new self();

        //always add the model to email!
        $params['object'] = $model;

        self::mergeDefaultMailSettings($mail, $emailDocument);
        self::addRecipients($mail, $emailDocument, $recipient);

        $mail->setDocument($emailDocument);
        $mail->setParams($params);
        $mail->setEnableLayoutOnPlaceholderRendering(false);

        $mail->send();

        if ($model instanceof Order) {
            static::addOrderNote($model, $emailDocument, $mail);
        }

        return true;
    }

    /**
     * Sends Messaging Mail
     *
     * @param $emailDocument
     * @param Message $message
     * @param string $recipient
     * @param array $params
     *
     * @return bool
     */
    public static function sendMessagingMail($emailDocument, Message $message, $recipient, $params = [])
    {
        $thread = $message->getThread();
        $shopId = $thread->getShopId();

        $shop = Shop::getById($shopId);

        if ($shop instanceof Shop) {
            //init Template
            \CoreShop::getTools()->initTemplateForShop($shop);
        }

        $mail = new self();

        self::mergeDefaultMailSettings($mail, $emailDocument);

        //always add the model to email!
        $params['object'] = $message;

        $mail->setDocument($emailDocument);
        $mail->setParams($params);
        $mail->setEnableLayoutOnPlaceholderRendering(false);
        $mail->addTo($recipient);
        $mail->send();

        if ($thread->getOrder() instanceof Order) {
            static::addOrderNote($thread->getOrder(), $emailDocument, $mail, ['threadId' => $message->getThreadId(), 'messageId' => $message->getId()]);
        }

        return true;
    }

    /**
     * Send email which belongs to an order
     *
     * @param $emailDocument
     * @param Order $order
     * @param bool $sendInvoices
     * @param bool $sendShipments
     * @param array $params
     *
     * @throws Exception\UnsupportedException
     * @throws \Exception
     *
     * @return bool
     */
    public static function sendOrderMail($emailDocument, Order $order, $sendInvoices = false, $sendShipments = false, $params = [])
    {
        if (!$emailDocument instanceof Document\Email) {
            return false;
        }

        //init Template
        \CoreShop::getTools()->initTemplateForShop($order->getShop());

        $emailParameters = array_merge($order->getCustomer()->getObjectVars(), $params);
        $emailParameters['orderTotal'] = \CoreShop::getTools()->formatPrice($order->getTotal());
        $emailParameters['orderNumber'] = $order->getOrderNumber();

        //always add the model to email!
        $emailParameters['object'] = $order;

        unset($emailParameters['____pimcore_cache_item__']);

        $recipient = [
            [$order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname() . ' ' . $order->getCustomer()->getLastname()]
        ];

        $mail = new self();

        self::mergeDefaultMailSettings($mail, $emailDocument);
        self::addRecipients($mail, $emailDocument, $recipient);

        $mail->setDocument($emailDocument);
        $mail->setParams($emailParameters);
        $mail->setEnableLayoutOnPlaceholderRendering(false);

        if ($sendInvoices && (bool)Configuration::get('SYSTEM.INVOICE.CREATE')) {
            $invoices = $order->getInvoices();

            foreach ($invoices as $invoice) {
                if ($invoice instanceof Order\Invoice) {
                    $asset = $invoice->getAsset();

                    if (!$asset instanceof Asset) {
                        $asset = $invoice->generate();
                    }

                    $attachment = new \Zend_Mime_Part($asset->getData());
                    $attachment->type = $asset->getMimetype();
                    $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                    $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                    $attachment->filename = $asset->getFilename();

                    $mail->addAttachment($attachment);
                }
            }
        }

        if ($sendShipments && (bool)Configuration::get('SYSTEM.SHIPMENT.CREATE')) {
            $shipments = $order->getShipments();

            foreach ($shipments as $shipment) {
                if ($shipment instanceof Order\Shipment) {
                    $asset = $shipment->getAsset();

                    if (!$asset instanceof Asset) {
                        $asset = $shipment->generate();
                    }

                    $attachment = new \Zend_Mime_Part($asset->getData());
                    $attachment->type = $asset->getMimetype();
                    $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                    $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                    $attachment->filename = $asset->getFilename();

                    $mail->addAttachment($attachment);
                }
            }
        }

        $mail->send();

        static::addOrderNote($order, $emailDocument, $mail);

        return true;
    }

    /**
     * @param Order          $order
     * @param Document\Email $emailDocument
     * @param Mail           $mail
     * @param array          $params
     *
     * @return bool
     */
    protected static function addOrderNote(Order $order, Document\Email $emailDocument, Mail $mail, $params = [])
    {
        $translate = \CoreShop::getTools()->getTranslate();

        $note = $order->createNote(Order::NOTE_EMAIL);
        $note->setTitle($translate->translate('coreshop_note_email'));
        $note->setDescription($translate->translate('coreshop_note_email_description'));

        $note->addData('document', 'text', $emailDocument->getId());
        $note->addData('recipient', 'text', implode(', ', (array) $mail->getRecipients()));
        $note->addData('subject', 'text', $mail->getSubjectRendered());

        foreach ($params as $key => $value) {
            $note->addData($key, 'text', $value);
        }

        //Because logger does not return any id, we need to fetch the last one!
        $listing = new Log\Listing();
        $listing->addConditionParam('documentId = ?', $emailDocument->getId());
        $listing->setOrderKey('sentDate');
        $listing->setOrder('desc');
        $listing->setLimit(1);
        $logData = $listing->load();

        if (isset($logData[0]) && $logData[0] instanceof Log) {
            $note->addData('email-log', 'text', $logData[0]->getId());
        }

        $note->save();

        return true;
    }

    /**
     * @param self $mail
     * @param Document\Email $emailDocument
     */
    private static function mergeDefaultMailSettings($mail, $emailDocument)
    {
        $from = $emailDocument->getFrom();

        if (!empty($from)) {
            $mail->setFrom($from);
        }

        $mail->addCc($emailDocument->getCcAsArray());
        $mail->addBcc($emailDocument->getBccAsArray());
    }

    /**
     * @param self $mail
     * @param Document\Email $emailDocument
     * @param string|array $recipients
     */
    private static function addRecipients($mail, $emailDocument, $recipients = '')
    {
        $to = [];
        if (is_array($recipients)) {
            foreach ($recipients as $_recipient) {
                if (is_array($_recipient)) {
                    $to[] = [$_recipient[0], $_recipient[1]];
                } else {
                    $multiRecipients = array_filter(explode(';', $_recipient));
                    foreach ($multiRecipients as $_multiRecipient) {
                        $to[] = [$_multiRecipient, ''];
                    }
                }
            }
        } else {
            $multiRecipients = array_filter(explode(';', $recipients));
            foreach ($multiRecipients as $_multiRecipient) {
                $to[] = [$_multiRecipient, ''];
            }
        }

        //now add recipients from emailDocument, if given.
        $storedRecipients = array_filter(explode(';', $emailDocument->getTo()));
        foreach ($storedRecipients as $_multiRecipient) {
            $to[] = [$_multiRecipient, ''];
        }

        foreach ($to as $recipient) {
            $mail->addTo($recipient[0], $recipient[1]);
        }
    }
}
