<?php

namespace CoreShop\Component\Core\Order;

use CoreShop\Bundle\CurrencyBundle\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use Pimcore\Mail;
use Pimcore\Model\Document;

class OrderMailProcessor implements OrderMailProcessorInterface
{
    /**
     * @var MoneyFormatterInterface
     */
    private $priceFormatter;

    /**
     * @var OrderInvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @param MoneyFormatterInterface $priceFormatter
     * @param OrderInvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(MoneyFormatterInterface $priceFormatter, OrderInvoiceRepositoryInterface $invoiceRepository)
    {
        $this->priceFormatter = $priceFormatter;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function sendOrderMail($emailDocument, OrderInterface $order, $sendInvoices = false, $sendShipments = false, $params = [])
    {
        if (!$emailDocument instanceof Document\Email) {
            return false;
        }

        $emailParameters = array_merge($order->getCustomer()->getObjectVars(), $params);
        $emailParameters['orderTotal'] = $this->priceFormatter->format($order->getTotal(), $order->getCurrency()->getIsoCode());
        $emailParameters['orderNumber'] = $order->getOrderNumber();

        //always add the model to email!
        $emailParameters['object'] = $order;

        unset($emailParameters['____pimcore_cache_item__'], $emailParameters['__dataVersionTimestamp']);

        $recipient = [
            [$order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname() . ' ' . $order->getCustomer()->getLastname()]
        ];

        $mail = new Mail();

        $this->addRecipients($mail, $emailDocument, $recipient);

        $mail->setDocument($emailDocument);
        $mail->setParams($emailParameters);
        $mail->setEnableLayoutOnPlaceholderRendering(false);

        if ($sendInvoices) { //TODO: Should invoice creation be configurable?
            $invoices = $this->invoiceRepository->getDocuments($order);

            foreach ($invoices as $invoice) {
                /*if ($invoice instanceof OrderInvoiceInterface) {
                    $attachment = \Swift_Attachment::fromPath($asset->getData());
                    $attachment->type = $asset->getMimetype();
                    $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                    $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
                    $attachment->filename = $asset->getFilename();

                    $mail->addAttachment($attachment);
                }*/
            }
        }

        /*if ($sendShipments && (bool)Configuration::get('SYSTEM.SHIPMENT.CREATE')) {
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
        }*/

        //$this->addOrderNote($order, $emailDocument, $mail);

        $mail->send();

        return true;
    }

    /**
     * @param Mail $mail
     * @param Document\Email $emailDocument
     * @param string|array $recipients
     */
    private function addRecipients($mail, $emailDocument, $recipients = '')
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