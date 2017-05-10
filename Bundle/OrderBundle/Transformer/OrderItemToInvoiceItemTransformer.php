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

namespace CoreShop\Bundle\OrderBundle\Transformer;

use CoreShop\Component\Resource\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;
use Pimcore\Model\Object\Fieldcollection;
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
     * @param ObjectServiceInterface              $objectService
     * @param string                              $pathForItems
     * @param TransformerEventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems,
        TransformerEventDispatcherInterface $eventDispatcher
    ) {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
        $this->eventDispatcher = $eventDispatcher;
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

        $this->setDocumentItemTaxes($orderItem, $invoiceItem, $invoiceItem->getTotal(false));

        $invoiceItem->save();

        $this->eventDispatcher->dispatchPostEvent('invoice_item', $invoiceItem, ['invoice' => $invoice, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        return $invoiceItem;
    }

    protected function setDocumentItemTaxes(OrderItemInterface $orderItem, OrderInvoiceItemInterface $docItem, $quantity)
    {
        $itemTaxes = new Fieldcollection();
        $totalTax = 0;

        /*$orderTaxes = $orderItem->getTaxes();

        if (is_array($orderTaxes)) {
            foreach ($orderTaxes as $tax) {
                if ($tax instanceof Order\Tax) {
                    $taxRate = Tax::create();
                    $taxRate->setRate($tax->getRate());

                    $taxCalculator = new TaxCalculator([$taxRate]);

                    $itemTax = Order\Tax::create([
                        'name' => $tax->getName(),
                        'rate' => $tax->getRate(),
                        'amount' => $taxCalculator->getTaxesAmount($quantity)
                    ]);

                    $itemTaxes->add($itemTax);

                    $totalTax += $itemTax->getAmount();
                }
            }
        }

        $docItem->setTotalTax($totalTax);
        $docItem->setTaxes($itemTaxes);*/
    }
}
