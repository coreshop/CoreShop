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
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Pimcore\Model\DataObject\Fieldcollection;
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
     * @var FactoryInterface
     */
    protected $taxItemFactory;

    /**
     * @var ObjectManager
     */
    protected $objectManger;

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
     * @param FactoryInterface $taxItemFactory
     * @param ObjectManager $objectManager
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
        FactoryInterface $taxItemFactory,
        ObjectManager $objectManager
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
        $this->taxItemFactory = $taxItemFactory;
        $this->objectManger = $objectManager;
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

        $this->objectManger->persist($invoice);

        /*
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function() {
            $this->objectManger->flush();
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

        $this->calculateInvoice($invoice);

        $this->objectManger->persist($invoice);

        VersionHelper::useVersioning(function() {
            $this->objectManger->flush();
        }, false);


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
        $this->calculateShipping($invoice, true);
        $this->calculateShipping($invoice, false);
        $this->calculateDiscount($invoice, true);
        $this->calculateDiscount($invoice, false);
        $this->calculateTotal($invoice, true);
        $this->calculateTotal($invoice, false);
    }

    /**
     * @param OrderInvoiceInterface $invoice
     * @param boolean $base Calculate Subtotal for Base Values
     */
    private function calculateSubtotal(OrderInvoiceInterface $invoice, $base = true)
    {
        $discountPercentage = $invoice->getOrder()->getDiscountPercentage();

        $subtotalWithTax = 0;
        $subtotalWithoutTax = 0;
        $subtotalTax = 0;

        /**
         * @var $item OrderInvoiceItemInterface
         */
        foreach ($invoice->getItems() as $item) {
            if ($base) {
                $subtotalWithTax += $item->getBaseTotal();
                $subtotalWithoutTax += $item->getBaseTotal(false);
                $subtotalTax += $item->getBaseTotalTax();
            } else {
                $subtotalWithTax += $item->getTotal();
                $subtotalWithoutTax += $item->getTotal(false);
                $subtotalTax += $item->getTotalTax();
            }

            foreach ($item->getTaxes() as $tax) {
                if ($tax instanceof TaxItemInterface) {
                    $this->addTax($invoice, $tax->getName(), $tax->getRate(), $tax->getAmount() * $discountPercentage, $base);
                }
            }
        }

        if ($base) {
            $invoice->setBaseSubtotal($subtotalWithTax);
            $invoice->setBaseSubtotal($subtotalWithoutTax, false);
            $invoice->setBaseSubtotalTax($subtotalTax);
        } else {
            $invoice->setSubtotal($subtotalWithTax);
            $invoice->setSubtotal($subtotalWithoutTax, false);
            $invoice->setSubtotalTax($subtotalTax);
        }
    }

    /**
     * Calculate Shipping Prices for invoices.
     *
     * @param OrderInvoiceInterface $invoice
     * @param boolean $base Calculate Shipping for Base Values
     */
    private function calculateShipping(OrderInvoiceInterface $invoice, $base = true)
    {
        $shippingWithTax = 0;
        $shippingWithoutTax = 0;
        $shippingTax = 0;

        if ($base) {
            $totalShipping = $invoice->getOrder()->getBaseShipping();
            $totalShippingWT = $invoice->getOrder()->getBaseShipping(false);
            $invoicedShipping = $this->getProcessedValue('baseShippingGross', $invoice->getOrder());
            $invoicedShippingWT = $this->getProcessedValue('baseShippingNet', $invoice->getOrder());
        } else {
            $totalShipping = $invoice->getOrder()->getShipping();
            $totalShippingWT = $invoice->getOrder()->getShipping(false);
            $invoicedShipping = $this->getProcessedValue('shippingGross', $invoice->getOrder());
            $invoicedShippingWT = $this->getProcessedValue('shippingNet', $invoice->getOrder());
        }

        if ($totalShipping - $invoicedShipping > 0) {
            $shippingTaxRate = $invoice->getOrder()->getShippingTaxRate();

            $shippingWithTax = $totalShipping - $invoicedShipping;
            $shippingWithoutTax = $totalShippingWT - $invoicedShippingWT;
            $shippingTax = $shippingWithTax - $shippingWithoutTax;

            $this->addTax($invoice, 'shipping', $shippingTaxRate, $shippingTax, $base);
        }

        if ($base) {
            $invoice->setBaseShipping($shippingWithTax);
            $invoice->setBaseShipping($shippingWithoutTax, false);
            $invoice->setBaseShippingTax($shippingTax);
        } else {
            $invoice->setShipping($shippingWithTax);
            $invoice->setShipping($shippingWithoutTax, false);
            $invoice->setShippingTax($shippingTax);
            $invoice->setShippingTaxRate($invoice->getOrder()->getShippingTaxRate());
        }
    }

    /**
     * Calculate Discount for Invoice.
     *
     * @param OrderInvoiceInterface $invoice
     * @param boolean $base Calculate Discount for Base Values
     */
    private function calculateDiscount(OrderInvoiceInterface $invoice, $base = true)
    {
        $discountWithTax = 0;
        $discountWithoutTax = 0;
        $discountTax = 0;

        if ($base) {
            $totalDiscount = $invoice->getOrder()->getBaseDiscount();
            $invoicedDiscount = $this->getProcessedValue('baseDiscount', $invoice->getOrder());
        } else {
            $totalDiscount = $invoice->getOrder()->getDiscount();
            $invoicedDiscount = $this->getProcessedValue('discount', $invoice->getOrder());
        }

        if ($totalDiscount - $invoicedDiscount > 0) {
            $discountWithTax = $totalDiscount - $invoicedDiscount;
            $discountWithoutTax = $invoice->getOrder()->getDiscount(false) - $this->getProcessedValue('discountNet', $invoice->getOrder());
            $discountTax = $discountWithTax - $discountWithoutTax;
        }

        if ($base) {
            $invoice->setBaseDiscount($discountWithTax);
            $invoice->setBaseDiscount($discountWithoutTax, false);
            $invoice->setBaseDiscountTax($discountTax);
        } else {
            $invoice->setDiscount($discountWithTax);
            $invoice->setDiscount($discountWithoutTax, false);
            $invoice->setDiscountTax($discountTax);
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
            $subtotalTax = $invoice->getBaseSubtotalTax();
            $shippingTax = $invoice->getBaseShippingTax();
            $discountTax = $invoice->getBaseDiscountTax();

            $subtotalWithTax = $invoice->getBaseSubtotal();
            $shippingWithTax = $invoice->getBaseShipping();
            $discountWithTax = $invoice->getBaseDiscount();

            $subtotalWithoutTax = $invoice->getBaseSubtotal(false);
            $shippingWithoutTax = $invoice->getBaseShipping(false);
            $discountWithoutTax = $invoice->getBaseDiscount(false);
        } else {
            $subtotalTax = $invoice->getSubtotalTax();
            $shippingTax = $invoice->getShippingTax();
            $discountTax = $invoice->getDiscountTax();

            $subtotalWithTax = $invoice->getSubtotal();
            $shippingWithTax = $invoice->getShipping();
            $discountWithTax = $invoice->getDiscount();

            $subtotalWithoutTax = $invoice->getSubtotal(false);
            $shippingWithoutTax = $invoice->getShipping(false);
            $discountWithoutTax = $invoice->getDiscount(false);
        }

        $totalTax = ($subtotalTax + $shippingTax) - $discountTax;
        $total = ($subtotalWithTax + $shippingWithTax) - $discountWithTax;
        $totalWithoutTax = ($subtotalWithoutTax + $shippingWithoutTax) - $discountWithoutTax;

        if ($base) {
            $invoice->setBaseTotalTax($totalTax);
            $invoice->setBaseTotal($total);
            $invoice->setBaseTotal($totalWithoutTax, false);
        } else {
            $invoice->setTotalTax($totalTax);
            $invoice->setTotal($total);
            $invoice->setTotal($totalWithoutTax, false);
        }
    }

    /**
     * @param string $field
     * @param OrderInterface $order
     *
     * @return float
     */
    private function getProcessedValue($field, OrderInterface $order)
    {
        $invoices = $this->invoiceRepository->getDocumentsNotInState($order, OrderInvoiceStates::STATE_CANCELLED);
        $processedValue = 0;

        foreach ($invoices as $invoice) {
            $processedValue += $invoice->getValueForFieldName($field);
        }

        return $processedValue;
    }

    /**
     * @param OrderInvoiceInterface $invoice
     * @param $name
     * @param $rate
     * @param $amount
     * @param boolean $base
     */
    private function addTax(OrderInvoiceInterface $invoice, $name, $rate, $amount, $base = true)
    {
        if ($base) {
            $taxes = $invoice->getBaseTaxes();
        } else {
            $taxes = $invoice->getTaxes();
        }

        if (!$taxes instanceof Fieldcollection) {
            $taxes = new Fieldcollection();
        }

        $found = false;

        foreach ($taxes as $tax) {
            if ($tax instanceof TaxItemInterface) {
                if ($tax->getName() === $name) {
                    $tax->setAmount($tax->getAmount() + $amount);
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            /**
             * @var $taxItem TaxItemInterface
             */
            $taxItem = $this->taxItemFactory->createNew();
            $taxItem->setName($name);
            $taxItem->setRate($rate);
            $taxItem->setAmount($amount);

            $taxes->add($taxItem);

            if ($base) {
                $invoice->setBaseTaxes($taxes);
            } else {
                $invoice->setTaxes($taxes);
            }
        }
    }
}
