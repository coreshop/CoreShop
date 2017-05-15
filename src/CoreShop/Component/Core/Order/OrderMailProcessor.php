<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Core\Order;

use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
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
     * @var OrderShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var OrderDocumentRendererInterface
     */
    private $orderDocumentRenderer;

    /**
     * @param MoneyFormatterInterface          $priceFormatter
     * @param OrderInvoiceRepositoryInterface  $invoiceRepository
     * @param OrderShipmentRepositoryInterface $shipmentRepository
     * @param OrderDocumentRendererInterface   $orderDocumentRenderer
     */
    public function __construct(
        MoneyFormatterInterface $priceFormatter,
        OrderInvoiceRepositoryInterface $invoiceRepository,
        OrderShipmentRepositoryInterface $shipmentRepository,
        OrderDocumentRendererInterface $orderDocumentRenderer
    ) {
        $this->priceFormatter = $priceFormatter;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderDocumentRenderer = $orderDocumentRenderer;
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
            [$order->getCustomer()->getEmail(), $order->getCustomer()->getFirstname().' '.$order->getCustomer()->getLastname()],
        ];

        $mail = new Mail();

        $this->addRecipients($mail, $emailDocument, $recipient);

        $mail->setDocument($emailDocument);
        $mail->setParams($emailParameters);
        $mail->setEnableLayoutOnPlaceholderRendering(false);

        if ($sendInvoices) { //TODO: Should invoice creation be configurable?
            $invoices = $this->invoiceRepository->getDocuments($order);

            foreach ($invoices as $invoice) {
                if ($invoice instanceof OrderInvoiceInterface) {
                    $data = $this->orderDocumentRenderer->renderDocumentPdf($invoice);

                    $mail->attach(\Swift_Attachment::newInstance($data, 'invoice.pdf', 'application/pdf'));
                }
            }
        }

        if ($sendShipments) {  //TODO: Should shipment creation be configurable?
            $shipments = $this->shipmentRepository->getDocuments($order);

            foreach ($shipments as $shipment) {
                if ($shipment instanceof OrderShipmentInterface) {
                    $data = $this->orderDocumentRenderer->renderDocumentPdf($shipment);

                    $mail->attach(\Swift_Attachment::newInstance($data, 'shipment.pdf', 'application/pdf'));
                }
            }
        }

        //$this->addOrderNote($order, $emailDocument, $mail);

        $mail->send();

        return true;
    }

    /**
     * @param Mail           $mail
     * @param Document\Email $emailDocument
     * @param string|array   $recipients
     */
    private function addRecipients($mail, $emailDocument, $recipients = '')
    {
        $to = [];
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                if (is_array($recipient)) {
                    $to[] = [$recipient[0], $recipient[1]];
                } else {
                    $multiRecipients = array_filter(explode(';', $recipient));
                    foreach ($multiRecipients as $multiRecipient) {
                        $to[] = [$multiRecipient, ''];
                    }
                }
            }
        } else {
            $multiRecipients = array_filter(explode(';', $recipients));
            foreach ($multiRecipients as $multiRecipient) {
                $to[] = [$multiRecipient, ''];
            }
        }

        //now add recipients from emailDocument, if given.
        $storedRecipients = array_filter(explode(';', $emailDocument->getTo()));
        foreach ($storedRecipients as $multiRecipient) {
            $to[] = [$multiRecipient, ''];
        }

        foreach ($to as $recipient) {
            $mail->addTo($recipient[0], $recipient[1]);
        }
    }
}
