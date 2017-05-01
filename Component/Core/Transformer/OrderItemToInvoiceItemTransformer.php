<?php

namespace CoreShop\Component\Core\Transformer;

use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
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
     * @param ObjectServiceInterface $objectService
     * @param string $pathForItems
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems
    )
    {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(OrderDocumentInterface $invoice, OrderItemInterface $orderItem, OrderDocumentItemInterface $invoiceItem, $quantity)
    {
        /**
         * @var $invoice OrderInvoiceInterface
         * @var $orderItem OrderItemInterface
         * @var $invoiceItem OrderInvoiceItemInterface
         */
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($invoice, OrderDocumentInterface::class);
        Assert::isInstanceOf($invoiceItem, OrderDocumentItemInterface::class);

        $itemFolder = $this->objectService->createFolderByPath($invoice->getFullPath() . '/' . $this->pathForItems);

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