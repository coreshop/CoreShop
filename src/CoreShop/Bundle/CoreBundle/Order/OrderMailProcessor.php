<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Order;

use CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface;
use CoreShop\Component\Core\Order\OrderMailProcessorInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Pimcore\Mail\MailProcessorInterface;
use Monolog\Logger;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document\Email;

class OrderMailProcessor implements OrderMailProcessorInterface
{
    public function __construct(
        private Logger $logger,
        private MoneyFormatterInterface $priceFormatter,
        private OrderInvoiceRepositoryInterface $invoiceRepository,
        private OrderShipmentRepositoryInterface $shipmentRepository,
        private OrderDocumentRendererInterface $orderDocumentRenderer,
        private ThemeHelperInterface $themeHelper,
        private MailProcessorInterface $mailProcessor,
    ) {
    }

    public function sendOrderMail(Email $emailDocument, OrderInterface $order, bool $sendInvoices = false, bool $sendShipments = false, array $params = []): bool
    {
        $emailParameters = [];
        $attachments = [];
        $customer = $order->getCustomer();

        if ($customer instanceof Concrete) {
            $emailParameters = array_merge($customer->getObjectVars(), $params);
        }

        $emailParameters['orderTotal'] = $this->priceFormatter->format($order->getTotal(), $order->getBaseCurrency()->getIsoCode());
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
                        $attachments[] = [
                            'body' => $data,
                            'name' => sprintf('invoice-%s.pdf', $invoice->getInvoiceNumber()),
                            'content-type' => 'application/pdf',
                        ];
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
                        $attachments[] = [
                            'body' => $data,
                            'name' => sprintf('shipment-%s.pdf', $shipment->getShipmentNumber()),
                            'content-type' => 'application/pdf',
                        ];
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
