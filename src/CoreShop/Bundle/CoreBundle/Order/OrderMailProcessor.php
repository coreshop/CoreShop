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

namespace CoreShop\Bundle\CoreBundle\Order;

use CoreShop\Bundle\StoreBundle\Theme\ThemeHelperInterface;
use CoreShop\Component\Core\Order\OrderMailProcessorInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Monolog\Logger;
use Pimcore\Mail;
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
     * @var NoteServiceInterface
     */
    private $noteService;

    /**
     * @var ThemeHelperInterface
     */
    private $themeHelper;

    /**
     * @param Logger $logger
     * @param MoneyFormatterInterface $priceFormatter
     * @param OrderInvoiceRepositoryInterface $invoiceRepository
     * @param OrderShipmentRepositoryInterface $shipmentRepository
     * @param OrderDocumentRendererInterface $orderDocumentRenderer
     * @param NoteServiceInterface $noteService
     * @param ThemeHelperInterface $themeHelper
     */
    public function __construct(
        Logger $logger,
        MoneyFormatterInterface $priceFormatter,
        OrderInvoiceRepositoryInterface $invoiceRepository,
        OrderShipmentRepositoryInterface $shipmentRepository,
        OrderDocumentRendererInterface $orderDocumentRenderer,
        NoteServiceInterface $noteService,
        ThemeHelperInterface $themeHelper
    )
    {
        $this->logger = $logger;
        $this->priceFormatter = $priceFormatter;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderDocumentRenderer = $orderDocumentRenderer;
        $this->noteService = $noteService;
        $this->themeHelper = $themeHelper;
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
            [
                $order->getCustomer()->getEmail(),
                $order->getCustomer()->getFirstname() . ' ' . $order->getCustomer()->getLastname()
            ],
        ];

        $mail = new Mail();

        $this->addRecipients($mail, $emailDocument, $recipient);

        $mail->setDocument($emailDocument);
        $mail->setParams($emailParameters);
        $mail->setEnableLayoutOnPlaceholderRendering(false);

        if ($sendInvoices) {
            $invoices = $this->invoiceRepository->getDocumentsInState($order, InvoiceStates::STATE_COMPLETE);
            foreach ($invoices as $invoice) {
                if ($invoice instanceof OrderInvoiceInterface) {
                    try {
                        $data = $this->orderDocumentRenderer->renderDocumentPdf($invoice);
                        $mail->attach(\Swift_Attachment::newInstance($data, 'invoice.pdf', 'application/pdf'));
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
                        $mail->attach(\Swift_Attachment::newInstance($data, 'shipment.pdf', 'application/pdf'));
                    } catch (\Exception $e) {
                        $this->logger->error('Error while attaching packing slip to order mail. Messages was: ' . $e->getMessage(), [$e]);
                    }
                }
            }
        }

        $this->themeHelper->useTheme($order->getStore()->getTemplate(), function () use ($mail) {
            $mail->send();
        });

        $this->addOrderNote($order, $emailDocument, $mail);

        return true;
    }

    /**
     * @param Mail $mail
     * @param Document\Email $emailDocument
     * @param string|array $recipients
     */
    private function addRecipients($mail, $emailDocument, $recipients = '')
    {
        $toRecipients = [];
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                if (is_array($recipient)) {
                    $toRecipients[] = [$recipient[0], $recipient[1]];
                } else {
                    $multiRecipients = array_filter(explode(';', $recipient));
                    foreach ($multiRecipients as $multiRecipient) {
                        $toRecipients[] = [$multiRecipient, ''];
                    }
                }
            }
        } else {
            $multiRecipients = array_filter(explode(';', $recipients));
            foreach ($multiRecipients as $multiRecipient) {
                $toRecipients[] = [$multiRecipient, ''];
            }
        }

        //now add recipients from emailDocument, if given.
        $storedRecipients = array_filter(explode(';', $emailDocument->getTo()));
        foreach ($storedRecipients as $multiRecipient) {
            $toRecipients[] = [$multiRecipient, ''];
        }

        foreach ($toRecipients as $recipient) {
            $mail->addTo($recipient[0], $recipient[1]);
        }
    }

    /**
     * @param OrderInterface $order
     * @param Document\Email $emailDocument
     * @param Mail $mail
     * @param array $params
     * @return bool
     */
    private function addOrderNote(OrderInterface $order, Document\Email $emailDocument, Mail $mail, $params = [])
    {
        $noteInstance = $this->noteService->createPimcoreNoteInstance($order, Notes::NOTE_EMAIL);

        $noteInstance->setTitle('Order Mail');

        $noteInstance->addData('document', 'text', $emailDocument->getId());
        $noteInstance->addData('recipient', 'text', implode(', ', (array)$mail->getTo()));
        $noteInstance->addData('subject', 'text', $mail->getSubjectRendered());

        foreach ($params as $key => $value) {
            $noteInstance->addData($key, 'text', $value);
        }

        $this->noteService->storeNoteForEmail($noteInstance, $emailDocument);

        return true;
    }
}

class_alias(OrderMailProcessor::class, 'CoreShop\Component\Core\Order\OrderMailProcessor');