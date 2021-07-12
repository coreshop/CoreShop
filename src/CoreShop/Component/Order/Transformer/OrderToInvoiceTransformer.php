<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Transformer;

use Carbon\Carbon;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class OrderToInvoiceTransformer implements OrderDocumentTransformerInterface
{
    protected OrderDocumentItemTransformerInterface $orderItemToInvoiceItemTransformer;
    protected NumberGeneratorInterface $numberGenerator;
    protected string $invoiceFolderPath;
    protected ObjectServiceInterface $objectService;
    protected PimcoreRepositoryInterface $orderItemRepository;
    protected PimcoreFactoryInterface $invoiceItemFactory;
    protected OrderInvoiceRepositoryInterface $invoiceRepository;
    protected TransformerEventDispatcherInterface $eventDispatcher;
    protected AdjustmentFactoryInterface $adjustmentFactory;

    public function __construct(
        OrderDocumentItemTransformerInterface $orderDocumentItemTransformer,
        NumberGeneratorInterface $numberGenerator,
        string $invoiceFolderPath,
        ObjectServiceInterface $objectService,
        PimcoreRepositoryInterface $orderItemRepository,
        PimcoreFactoryInterface $invoiceItemFactory,
        OrderInvoiceRepositoryInterface $invoiceRepository,
        TransformerEventDispatcherInterface $eventDispatcher,
        AdjustmentFactoryInterface $adjustmentFactory
    ) {
        $this->orderItemToInvoiceItemTransformer = $orderDocumentItemTransformer;
        $this->numberGenerator = $numberGenerator;
        $this->invoiceFolderPath = $invoiceFolderPath;
        $this->objectService = $objectService;
        $this->orderItemRepository = $orderItemRepository;
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public function transform(OrderInterface $order, OrderDocumentInterface $invoice, $itemsToTransform)
    {
        /**
         * @var OrderInterface $order
         */
        Assert::isInstanceOf($order, OrderInterface::class);
        Assert::isInstanceOf($invoice, OrderInvoiceInterface::class);

        $this->eventDispatcher->dispatchPreEvent('invoice', $invoice, ['order' => $order, 'items' => $itemsToTransform]);

        $invoiceFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $order->getFullPath(), $this->invoiceFolderPath));

        $invoice->setOrder($order);

        $invoiceNumber = $this->numberGenerator->generate($invoice);

        /**
         * @var OrderInvoiceInterface $invoice
         */
        $invoice->setKey(Service::getValidKey($invoiceNumber, 'object'));
        $invoice->setInvoiceNumber($invoiceNumber);
        $invoice->setParent($invoiceFolder);
        $invoice->setPublished(true);
        $invoice->setInvoiceDate(Carbon::now());

        /*
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function () use ($invoice) {
            $invoice->save();
        }, false);

        $items = [];

        /**
         * @var OrderItemInterface $item
         */
        foreach ($itemsToTransform as $item) {
            $invoiceItem = $this->invoiceItemFactory->createNew();
            $orderItem = $this->orderItemRepository->find($item['orderItemId']);
            $quantity = $item['quantity'];

            if ($orderItem instanceof OrderItemInterface) {
                $items[] = $this->orderItemToInvoiceItemTransformer->transform(
                    $invoice,
                    $orderItem,
                    $invoiceItem,
                    $quantity,
                    $item
                );
            }
        }

        $invoice->setItems($items);

        VersionHelper::useVersioning(function () use ($invoice) {
            $invoice->save();
        }, false);

        $this->calculateInvoice($invoice);

        $this->eventDispatcher->dispatchPostEvent('invoice', $invoice, ['order' => $order, 'items' => $itemsToTransform]);

        return $invoice;
    }

    /**
     * @param OrderInvoiceInterface $invoice
     */
    private function calculateInvoice(OrderInvoiceInterface $invoice)
    {
        $this->calculateSubtotal($invoice, true);
        $this->calculateSubtotal($invoice, false);
        $this->calculateAdjustments($invoice, true);
        $this->calculateAdjustments($invoice, false);
        $this->calculateTotal($invoice, true);
        $this->calculateTotal($invoice, false);

        VersionHelper::useVersioning(function () use ($invoice) {
            $invoice->save();
        }, false);
    }

    /**
     * @param OrderInvoiceInterface $invoice
     * @param bool                  $converted    Calculate Subtotal for Base Values
     */
    private function calculateSubtotal(OrderInvoiceInterface $invoice, $converted = true)
    {
        $subtotalWithTax = 0;
        $subtotalWithoutTax = 0;

        /**
         * @var OrderInvoiceItemInterface $item
         */
        foreach ($invoice->getItems() as $item) {
            if ($converted) {
                $subtotalWithTax += $item->getConvertedTotal();
                $subtotalWithoutTax += $item->getConvertedTotal(false);
            } else {
                $subtotalWithTax += $item->getTotal();
                $subtotalWithoutTax += $item->getTotal(false);
            }
        }

        if ($converted) {
            $invoice->setConvertedSubtotal($subtotalWithTax);
            $invoice->setConvertedSubtotal($subtotalWithoutTax, false);
        } else {
            $invoice->setSubtotal($subtotalWithTax);
            $invoice->setSubtotal($subtotalWithoutTax, false);
        }
    }

    /**
     * Calculate all Adjustments for Invoice.
     *
     * @param OrderInvoiceInterface $invoice
     * @param bool                  $converted
     */
    private function calculateAdjustments(OrderInvoiceInterface $invoice, $converted = true)
    {
        $order = $invoice->getOrder();

        foreach ($converted ? $order->getConvertedAdjustments() : $order->getAdjustments() as $adjustment) {
            $orderAdjustmentsGross = $converted ? $order->getConvertedAdjustmentsTotal($adjustment->getTypeIdentifier()) : $order->getAdjustmentsTotal($adjustment->getTypeIdentifier());
            $orderAdjustmentsNet = $converted ? $order->getConvertedAdjustmentsTotal($adjustment->getTypeIdentifier(), false) : $order->getAdjustmentsTotal($adjustment->getTypeIdentifier(), false);

            $adjustmentValueToProcessGross = $orderAdjustmentsGross - $this->getProcessedAdjustmentValue($order, $adjustment->getTypeIdentifier(), true, $converted);
            $adjustmentValueToProcessNet = $orderAdjustmentsNet - $this->getProcessedAdjustmentValue($order, $adjustment->getTypeIdentifier(), false, $converted);

            if (0 !== $adjustmentValueToProcessGross) {
                $newAdjustment = $this->adjustmentFactory->createWithData($adjustment->getTypeIdentifier(), $adjustment->getLabel(), $adjustmentValueToProcessGross, $adjustmentValueToProcessNet, $adjustment->getNeutral());

                $converted ? $invoice->addConvertedAdjustment($newAdjustment) : $invoice->addAdjustment($newAdjustment);
            }
        }
    }

    /**
     * Calculate Total for invoice.
     *
     * @param OrderInvoiceInterface $invoice
     * @param bool                  $converted    Calculate Totals for Base Values
     */
    private function calculateTotal(OrderInvoiceInterface $invoice, $converted = true)
    {
        if ($converted) {
            $subtotalWithTax = $invoice->getConvertedSubtotal();
            $adjustmentsTotal = $invoice->getConvertedAdjustmentsTotal();

            $subtotalWithoutTax = $invoice->getConvertedSubtotal(false);
            $adjustmentsTotalWithoutTax = $invoice->getConvertedAdjustmentsTotal(null, false);
        } else {
            $subtotalWithTax = $invoice->getSubtotal();
            $adjustmentsTotal = $invoice->getAdjustmentsTotal();

            $subtotalWithoutTax = $invoice->getSubtotal(false);
            $adjustmentsTotalWithoutTax = $invoice->getAdjustmentsTotal(null, false);
        }

        $total = $subtotalWithTax + $adjustmentsTotal;
        $totalWithoutTax = $subtotalWithoutTax + $adjustmentsTotalWithoutTax;

        if ($converted) {
            $invoice->setConvertedTotal($total);
            $invoice->setConvertedTotal($totalWithoutTax, false);
        } else {
            $invoice->setTotal($total);
            $invoice->setTotal($totalWithoutTax, false);
        }
    }

    /**
     * @param OrderInterface $order
     * @param string         $adjustmentIdentifier
     * @param bool           $withTax
     * @param bool           $converted
     *
     * @return int
     */
    private function getProcessedAdjustmentValue(OrderInterface $order, $adjustmentIdentifier, bool $withTax, bool $converted)
    {
        $invoices = $this->invoiceRepository->getDocumentsNotInState($order, OrderInvoiceStates::STATE_CANCELLED);
        $processedValue = 0;

        /**
         * @var OrderInvoiceInterface $invoice
         */
        foreach ($invoices as $invoice) {
            foreach ($converted ? $invoice->getConvertedAdjustments() : $invoice->getAdjustments() as $adjustment) {
                if ($adjustment->getTypeIdentifier() === $adjustmentIdentifier) {
                    $processedValue += $adjustment->getAmount($withTax);
                }
            }
        }

        return $processedValue;
    }
}
