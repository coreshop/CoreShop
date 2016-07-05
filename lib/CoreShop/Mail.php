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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use CoreShop\Model\Configuration;
use CoreShop\Model\Messaging\Message;
use CoreShop\Model\Order;
use Pimcore\Mail as PimcoreMail;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;

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
     */
    public static function sendMessagingMail($emailDocument, Message $message, $recipient)
    {
        $mail = new self();
        $mail->setDocument($emailDocument);
        $mail->setParams(array('message' => $message->getMessage(), 'messageObject' => $message));
        $mail->setEnableLayoutOnPlaceholderRendering(false);
        $mail->addTo($recipient);
        $mail->send();
    }

    /**
     * Send email which belongs to an order
     *
     * @param $emailDocument
     * @param Order $order
     * @param Order\State $orderState
     * @param bool $allowBcc
     * @throws Exception\UnsupportedException
     * @throws \Exception
     */
    public static function sendOrderMail($emailDocument, Order $order, Order\State $orderState = null, $allowBcc = false)
    {
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

            if ($orderState instanceof Order\State) {
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

            if ($allowBcc === true) {
                $sendBccToUser = Configuration::get('SYSTEM.MAIL.ORDER.BCC');
                $adminMailAddress = Configuration::get('SYSTEM.MAIL.ORDER.NOTIFICATION');

                if ($sendBccToUser === true && !empty($adminMailAddress)) {
                    $mail->addBcc(explode(',', $adminMailAddress));
                }
            }

            $mail->send();
        }
    }
}
