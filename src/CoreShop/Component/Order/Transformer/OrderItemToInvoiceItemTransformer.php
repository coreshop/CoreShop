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

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxRulesTaxCalculator;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

class OrderItemToInvoiceItemTransformer implements OrderDocumentItemTransformerInterface
{
    /**
     * @var ObjectServiceInterface
     */
    private $objectService;

    /**
     * @var string
     */
    private $pathForItems;

    /**
     * @var TransformerEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var FactoryInterface
     */
    private $taxRateFactory;

    /**
     * @var FactoryInterface
     */
    private $taxItemFactory;

    /**
     * @param ObjectServiceInterface              $objectService
     * @param string                              $pathForItems
     * @param TransformerEventDispatcherInterface $eventDispatcher
     * @param FactoryInterface                    $taxRateFactory
     * @param FactoryInterface                    $taxItemFactory
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems,
        TransformerEventDispatcherInterface $eventDispatcher,
        FactoryInterface $taxRateFactory,
        FactoryInterface $taxItemFactory
    ) {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
        $this->eventDispatcher = $eventDispatcher;
        $this->taxRateFactory = $taxRateFactory;
        $this->taxItemFactory = $taxItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(OrderDocumentInterface $invoice, OrderItemInterface $orderItem, OrderDocumentItemInterface $invoiceItem, $quantity)
    {
        /*
         * @var $invoice OrderInvoiceInterface
         * @var $orderItem OrderItemInterface
         * @var $invoiceItem OrderInvoiceItemInterface
         */
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($invoice, OrderDocumentInterface::class);
        Assert::isInstanceOf($invoiceItem, OrderDocumentItemInterface::class);

        $this->eventDispatcher->dispatchPreEvent('invoice_item', $invoiceItem, ['invoice' => $invoice, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        $itemFolder = $this->objectService->createFolderByPath($invoice->getFullPath().'/'.$this->pathForItems);

        $invoiceItem->setKey($orderItem->getKey());
        $invoiceItem->setParent($itemFolder);
        $invoiceItem->setPublished(true);

        $invoiceItem->setOrderItem($orderItem);
        $invoiceItem->setQuantity($quantity);

        $invoiceItem->setTotal($orderItem->getItemPrice(true) * $quantity, true);
        $invoiceItem->setTotal($orderItem->getItemPrice(false) * $quantity, false);
        $invoiceItem->setTotalTax(($orderItem->getItemPrice(true) - $orderItem->getItemPrice(false)) * $quantity);

        $invoiceItem->setBaseTotal($orderItem->getBaseItemPrice(true) * $quantity, true);
        $invoiceItem->setBaseTotal($orderItem->getBaseItemPrice(false) * $quantity, false);
        $invoiceItem->setBaseTotalTax(($orderItem->getBaseItemPrice(true) - $orderItem->getBaseItemPrice(false)) * $quantity);

        $this->setDocumentItemTaxes($orderItem, $invoiceItem, $invoiceItem->getTotal(false), false);
        $this->setDocumentItemTaxes($orderItem, $invoiceItem, $invoiceItem->getTotal(false), true);

        VersionHelper::useVersioning(function () use ($invoiceItem) {
            $invoiceItem->save();
        }, false);

        $this->eventDispatcher->dispatchPostEvent('invoice_item', $invoiceItem, ['invoice' => $invoice, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        return $invoiceItem;
    }

    /**
     * @param OrderItemInterface        $orderItem
     * @param OrderInvoiceItemInterface $docItem
     * @param $quantity
     * @param bool $base
     */
    protected function setDocumentItemTaxes(OrderItemInterface $orderItem, OrderInvoiceItemInterface $docItem, $quantity, $base = true)
    {
        $itemTaxes = new Fieldcollection();
        $totalTax = 0;

        if ($base) {
            $orderTaxes = $orderItem->getBaseTaxes();
        } else {
            $orderTaxes = $orderItem->getTaxes();
        }

        if ($orderTaxes instanceof Fieldcollection) {
            foreach ($orderTaxes as $tax) {
                if ($tax instanceof TaxItemInterface) {
                    /**
                     * @var TaxRateInterface
                     */
                    $taxRate = $this->taxRateFactory->createNew();
                    $taxRate->setRate($tax->getRate());

                    $taxCalculator = new TaxRulesTaxCalculator([$taxRate]);

                    /**
                     * @var TaxItemInterface
                     */
                    $itemTax = $this->taxItemFactory->createNew();
                    $itemTax->setName($tax->getName());
                    $itemTax->setRate($tax->getRate());
                    $itemTax->setAmount($taxCalculator->getTaxesAmount($quantity));

                    $itemTaxes->add($itemTax);

                    $totalTax += $itemTax->getAmount();
                }
            }
        }

        if ($base) {
            $docItem->setBaseTotalTax($totalTax);
            $docItem->setBaseTaxes($itemTaxes);
        } else {
            $docItem->setTotalTax($totalTax);
            $docItem->setTaxes($itemTaxes);
        }
    }
}
