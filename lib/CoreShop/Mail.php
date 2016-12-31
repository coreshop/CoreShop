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

namespace CoreShop;

use CoreShop\Model\Configuration;
use CoreShop\Model\Messaging\Message;
use CoreShop\Model\Order;
use CoreShop\Model\Shop;
use Pimcore\Db;
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
     * Sends Messaging Mail
     *
     * @param $emailDocument
     * @param Message $message
     * @param string $recipient
     * @param array $params
     */
    public static function sendMessagingMail($emailDocument, Message $message, $recipient, $params = [])
    {
        $thread = $message->getThread();
        $shopId = $thread->getShopId();

        $shop = Shop::getById($shopId);

        //init Template
        \CoreShop::getTools()->initTemplateForShop($shop);

        $mail = new self();

        self::mergeDefaultMailSettings($mail, $emailDocument);

        $mail->setDocument($emailDocument);
        $mail->setParams(array_merge($params, ['message' => $mesesage->getMessage(), 'messageObject' => $message]));
        $mail->setEnableLayoutOnPlaceholderRendering(false);
        $mail->addTo($recipient);
        $mail->send();

        if($thread->getOrder() instanceof Order) {
            static::addOrderNote($thread->getOrder(), $emailDocument, $mail, true);
        }
    }

    /**
     * @param $emailDocument
     * @param $recipient
     * @param array $params
     */
    public static function sendMail($emailDocument, $recipient, $params = []) {
        //init Template
        //TODO: Shop init?
        //\CoreShop::getTools()->initTemplateForShop($shop);

        $mail = new self();

        self::mergeDefaultMailSettings($mail, $emailDocument);

        $mail->setDocument($emailDocument);
        $mail->setParams($params);
        $mail->setEnableLayoutOnPlaceholderRendering(false);
        $mail->addTo($recipient);
        $mail->send();
    }

    /**
     * Send email which belongs to an order
     *
     * @param $emailDocument
     * @param Order $order
     * @param bool $sendInvoices
     * @param bool $sendShipments
     *
     * @throws Exception\UnsupportedException
     * @throws \Exception
     */
    public static function sendOrderMail($emailDocument, Order $order, $sendInvoices = false, $sendShipments = false)
    {
        if ($emailDocument instanceof Document\Email) {
            //init Template
            \CoreShop::getTools()->initTemplateForShop($order->getShop());

            $emailParameters = $order->getCustomer()->getObjectVars();
            $emailParameters['orderTotal'] = \CoreShop::getTools()->formatPrice($order->getTotal());
            $emailParameters['order'] = $order;

            unset($emailParameters['____pimcore_cache_item__']);

            $mail = new self();

            self::mergeDefaultMailSettings($mail, $emailDocument);

            $mail->setDocument($emailDocument);
            $mail->setParams($emailParameters);
            $mail->setEnableLayoutOnPlaceholderRendering(false);
            $mail->addTo($order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname().' '.$order->getCustomer()->getLastname());

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
        }
    }

    /**
     * @param Order $order
     * @param Document\Email $emailDocument
     * @param $mail
     */
    private static function addOrderNote(Order $order, Document\Email $emailDocument, Mail $mail) {
        $translate = \CoreShop::getTools()->getTranslate();

        $note = $order->createNote(Order::NOTE_EMAIL);
        $note->setTitle($translate->translate('coreshop_note_email'));
        $note->setDescription($translate->translate('coreshop_note_email_description'));

        $log = Log::getById(Db::get()->lastInsertId());

        if($log instanceof Log) {
            $note->addData('email-log', 'text', $log->getId());
        }

        $note->addData('document', 'text', $emailDocument->getId());
        $note->addData('date', 'text', time());
        $note->addData('recipient', 'text', explode(', ', $mail->getRecipients()));
        $note->addData('subject', 'text', $mail->getSubjectRendered());
        $note->save();
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
}
