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

namespace CoreShop\Component\Order\Transformer;

use Carbon\Carbon;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
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
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use Webmozart\Assert\Assert;

class OrderToInvoiceTransformer implements OrderDocumentTransformerInterface
{
    /**
     * @var OrderDocumentItemTransformerInterface
     */
    protected $orderItemToInvoiceItemTransformer;

    /**
     * @var ItemKeyTransformerInterface
     */
    protected $keyTransformer;

    /**
     * @var NumberGeneratorInterface
     */
    protected $numberGenerator;

    /**
     * @var string
     */
    protected $invoiceFolderPath;

    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var PimcoreRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var PimcoreFactoryInterface
     */
    protected $invoiceItemFactory;

    /**
     * @var OrderInvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var TransformerEventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var AdjustmentFactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @param OrderDocumentItemTransformerInterface $orderDocumentItemTransformer
     * @param ItemKeyTransformerInterface $keyTransformer
     * @param NumberGeneratorInterface $numberGenerator
     * @param string $invoiceFolderPath
     * @param ObjectServiceInterface $objectService
     * @param PimcoreRepositoryInterface $orderItemRepository
     * @param PimcoreFactoryInterface $invoiceItemFactory
     * @param OrderInvoiceRepositoryInterface $invoiceRepository
     * @param TransformerEventDispatcherInterface $eventDispatcher
     * @param AdjustmentFactoryInterface $adjustmentFactory
     */
    public function __construct(
        OrderDocumentItemTransformerInterface $orderDocumentItemTransformer,
        ItemKeyTransformerInterface $keyTransformer,
        NumberGeneratorInterface $numberGenerator,
        $invoiceFolderPath,
        ObjectServiceInterface $objectService,
        PimcoreRepositoryInterface $orderItemRepository,
        PimcoreFactoryInterface $invoiceItemFactory,
        OrderInvoiceRepositoryInterface $invoiceRepository,
        TransformerEventDispatcherInterface $eventDispatcher,
        AdjustmentFactoryInterface $adjustmentFactory
    )
    {
        $this->orderItemToInvoiceItemTransformer = $orderDocumentItemTransformer;
        $this->keyTransformer = $keyTransformer;
        $this->numberGenerator = $numberGenerator;
        $this->invoiceFolderPath = $invoiceFolderPath;
        $this->objectService = $objectService;
        $this->orderItemRepository = $orderItemRepository;
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(OrderInterface $order, OrderDocumentInterface $invoice, $itemsToTransform)
    {
        /*
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($order, OrderInterface::class);
        Assert::isInstanceOf($invoice, OrderInvoiceInterface::class);

        $this->eventDispatcher->dispatchPreEvent('invoice', $invoice, ['order' => $order, 'items' => $itemsToTransform]);

        $invoiceFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $order->getFullPath(), $this->invoiceFolderPath));

        $invoice->setOrder($order);

        $invoiceNumber = $this->numberGenerator->generate($invoice);

        /**
         * @var $invoice OrderInvoiceInterface
         * @var $order OrderInterface
         */
        $invoice->setKey($this->keyTransformer->transform($invoiceNumber));
        $invoice->setInvoiceNumber($invoiceNumber);
        $invoice->setParent($invoiceFolder);
        $invoice->setPublished(true);
        $invoice->setInvoiceDate(Carbon::now());

        /*
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function() use ($invoice) {
            $invoice->save();
        }, false);

        $items = [];

        /**
         * @var $cartItem CartItemInterface
         */
        foreach ($itemsToTransform as $item) {
            $invoiceItem = $this->invoiceItemFactory->createNew();
            $orderItem = $this->orderItemRepository->find($item['orderItemId']);
            $quantity = $item['quantity'];

            if ($orderItem instanceof OrderItemInterface) {
                $items[] = $this->orderItemToInvoiceItemTransformer->transform($invoice, $orderItem, $invoiceItem, $quantity);
            }
        }

        $invoice->setItems($items);

        VersionHelper::useVersioning(function() use ($invoice) {
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

        VersionHelper::useVersioning(function() use ($invoice) {
            $invoice->save();
        }, false);
    }

    /**
     * @param OrderInvoiceInterface $invoice
     * @param boolean $base Calculate Subtotal for Base Values
     */
    private function calculateSubtotal(OrderInvoiceInterface $invoice, $base = true)
    {
        $subtotalWithTax = 0;
        $subtotalWithoutTax = 0;

        /**
         * @var $item OrderInvoiceItemInterface
         */
        foreach ($invoice->getItems() as $item) {
            if ($base) {
                $subtotalWithTax += $item->getBaseTotal();
                $subtotalWithoutTax += $item->getBaseTotal(false);
            } else {
                $subtotalWithTax += $item->getTotal();
                $subtotalWithoutTax += $item->getTotal(false);
            }
        }

        if ($base) {
            $invoice->setBaseSubtotal($subtotalWithTax);
            $invoice->setBaseSubtotal($subtotalWithoutTax, false);
        } else {
            $invoice->setSubtotal($subtotalWithTax);
            $invoice->setSubtotal($subtotalWithoutTax, false);
        }
    }

    /**
     * Calculate all Adjustments for Invoice
     *
     * @param OrderInvoiceInterface $invoice
     * @param bool                  $base
     */
    private function calculateAdjustments(OrderInvoiceInterface $invoice, $base = true)
    {
        $order = $invoice->getOrder();

        foreach ($base ? $order->getBaseAdjustments() : $order->getAdjustments() as $adjustment) {
            $orderAdjustmentsGross = $base ? $order->getBaseAdjustmentsTotal($adjustment->getTypeIdentifier()) : $order->getAdjustmentsTotal($adjustment->getTypeIdentifier());
            $orderAdjustmentsNet = $base ? $order->getBaseAdjustmentsTotal($adjustment->getTypeIdentifier(), false) : $order->getAdjustmentsTotal($adjustment->getTypeIdentifier(), false);

            $adjustmentValueToProcessGross = $orderAdjustmentsGross - $this->getProcessedAdjustmentValue($order, $adjustment->getTypeIdentifier(), true, $base);
            $adjustmentValueToProcessNet = $orderAdjustmentsNet - $this->getProcessedAdjustmentValue($order, $adjustment->getTypeIdentifier(), false, $base);

            if (0 !== $adjustmentValueToProcessGross) {
                $newAdjustment = $this->adjustmentFactory->createWithData($adjustment->getTypeIdentifier(), $adjustment->getLabel(), $adjustmentValueToProcessGross, $adjustmentValueToProcessNet, $adjustment->getNeutral());

                $base ? $invoice->addBaseAdjustment($newAdjustment) : $invoice->addAdjustment($newAdjustment);
            }
        }
    }

    /**
     * Calculate Total for invoice.
     *
     * @param OrderInvoiceInterface $invoice
     * @param boolean $base Calculate Totals for Base Values
     */
    private function calculateTotal(OrderInvoiceInterface $invoice, $base = true)
    {
        if ($base) {
            $subtotalWithTax = $invoice->getBaseSubtotal();
            $adjustmentsTotal = $invoice->getBaseAdjustmentsTotal();

            $subtotalWithoutTax = $invoice->getBaseSubtotal(false);
            $adjustmentsTotalWithoutTax = $invoice->getBaseAdjustmentsTotal(null, false);
        } else {
            $subtotalWithTax = $invoice->getSubtotal();
            $adjustmentsTotal = $invoice->getAdjustmentsTotal();

            $subtotalWithoutTax = $invoice->getSubtotal(false);
            $adjustmentsTotalWithoutTax = $invoice->getAdjustmentsTotal(null, false);
        }

        $total = $subtotalWithTax + $adjustmentsTotal;
        $totalWithoutTax = $subtotalWithoutTax + $adjustmentsTotalWithoutTax;

        if ($base) {
            $invoice->setBaseTotal($total);
            $invoice->setBaseTotal($totalWithoutTax, false);
        } else {
            $invoice->setTotal($total);
            $invoice->setTotal($totalWithoutTax, false);
        }
    }

    /**
     * @param OrderInterface $order
     * @param                $adjustmentIdentifier
     * @param bool           $withTax
     * @param bool           $base
     * @return int
     */
    private function getProcessedAdjustmentValue(OrderInterface $order, $adjustmentIdentifier, bool $withTax, bool $base)
    {
        $invoices = $this->invoiceRepository->getDocumentsNotInState($order, OrderInvoiceStates::STATE_CANCELLED);
        $processedValue = 0;

        /**
         * @var $invoice OrderInvoiceInterface
         */
        foreach ($invoices as $invoice) {
            foreach ($base ? $invoice->getBaseAdjustments() : $invoice->getAdjustments() as $adjustment) {
                if ($adjustment->getTypeIdentifier() === $adjustmentIdentifier) {
                    $processedValue += $adjustment->getAmount($withTax);
                }
            }
        }

        return $processedValue;
    }
}
