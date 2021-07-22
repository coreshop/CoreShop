<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Order;

use CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface;
use CoreShop\Component\Core\Order\OrderMailProcessorInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Pimcore\Mail\MailProcessorInterface;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use Monolog\Logger;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;

class OrderMailProcessor implements OrderMailProcessorInterface
{
    /**
     * @var Logger
     */
    private $logger;

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
     * @var ThemeHelperInterface
     */
    private $themeHelper;

    /**
     * @var MailProcessorInterface
     */
    private $mailProcessor;

    /**
     * @param Logger                           $logger
     * @param MoneyFormatterInterface          $priceFormatter
     * @param OrderInvoiceRepositoryInterface  $invoiceRepository
     * @param OrderShipmentRepositoryInterface $shipmentRepository
     * @param OrderDocumentRendererInterface   $orderDocumentRenderer
     * @param ThemeHelperInterface             $themeHelper
     * @param MailProcessorInterface           $mailProcessor
     */
    public function __construct(
        Logger $logger,
        MoneyFormatterInterface $priceFormatter,
        OrderInvoiceRepositoryInterface $invoiceRepository,
        OrderShipmentRepositoryInterface $shipmentRepository,
        OrderDocumentRendererInterface $orderDocumentRenderer,
        ThemeHelperInterface $themeHelper,
        MailProcessorInterface $mailProcessor
    ) {
        $this->logger = $logger;
        $this->priceFormatter = $priceFormatter;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderDocumentRenderer = $orderDocumentRenderer;
        $this->themeHelper = $themeHelper;
        $this->mailProcessor = $mailProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function sendOrderMail($emailDocument, OrderInterface $order, $sendInvoices = false, $sendShipments = false, $params = [])
    {
        if (!$emailDocument instanceof Document\Email) {
            return false;
        }

        $attachments = [];
        $customer = $order->getCustomer();

        if ($customer instanceof Concrete) {
            $emailParameters = array_merge($customer->getObjectVars(), $params);
        }

        $emailParameters['orderTotal'] = $this->priceFormatter->format($order->getTotal(), $order->getCurrency()->getIsoCode());
        $emailParameters['orderNumber'] = $order->getOrderNumber();

        //always add the model to email!
        $emailParameters['object'] = $order;
        $emailParameters['storeNote'] = true;

        unset($emailParameters['____pimcore_cache_item__'], $emailParameters['__dataVersionTimestamp']);

        $recipient = [];

        if (!isset($params['doNotSendToDesignatedRecipient']) || !$params['doNotSendToDesignatedRecipient']) {
            $recipient = [
                [
                    $customer->getEmail(),
                    $customer->getFirstname() . ' ' . $customer->getLastname(),
                ],
            ];
        }

        if ($sendInvoices) {
            $invoices = $this->invoiceRepository->getDocumentsInState($order, InvoiceStates::STATE_COMPLETE);

            foreach ($invoices as $invoice) {
                if ($invoice instanceof OrderInvoiceInterface) {
                    try {
                        $data = $this->orderDocumentRenderer->renderDocumentPdf($invoice);
                        $attachments[] = new \Swift_Attachment($data, sprintf('invoice-%s.pdf', $invoice->getInvoiceNumber()), 'application/pdf');
                    } catch (\Exception $e) {
                        $this->logger->error('Error while attaching invoice to order mail. Messages was: ' . $e->getMessage(), [$e]);
                    }
                }
            }
        }

        if ($sendShipments) {
            $shipments = $this->shipmentRepository->getDocumentsInState($order, ShipmentStates::STATE_SHIPPED);

            foreach ($shipments as $shipment) {
                if ($shipment instanceof OrderShipmentInterface) {
                    try {
                        $data = $this->orderDocumentRenderer->renderDocumentPdf($shipment);
                        $attachments[] = new \Swift_Attachment($data, sprintf('shipment-%s.pdf', $shipment->getShipmentNumber()), 'application/pdf');
                    } catch (\Exception $e) {
                        $this->logger->error('Error while attaching packing slip to order mail. Messages was: ' . $e->getMessage(), [$e]);
                    }
                }
            }
        }

        return $this->themeHelper->useTheme($order->getStore()->getTemplate(), function () use ($emailDocument, $order, $recipient, $attachments, $emailParameters) {
            return $this->mailProcessor->sendMail($emailDocument, $order, $recipient, $attachments, $emailParameters);
        });
    }
}
