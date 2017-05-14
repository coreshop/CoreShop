<?php

namespace CoreShop\Component\Order\Taxation;

use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Webmozart\Assert\Assert;

class CartItemTaxCollector implements ProposalItemTaxCollectorInterface
{
    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @param TaxCollectorInterface $taxCollector
     */
    public function __construct(TaxCollectorInterface $taxCollector)
    {
        $this->taxCollector = $taxCollector;
    }

    /**
     * @param ProposalItemInterface $proposalItem
     * @return TaxItemInterface[]
     */
    public function getTaxes(ProposalItemInterface $proposalItem)
    {
        Assert::isInstanceOf($proposalItem, CartItemInterface::class);

        $taxCalculator = $proposalItem->getProduct()->getTaxCalculator();
        $total = $proposalItem->getTotal(false);

        return $this->taxCollector->collectTaxes($taxCalculator, $total);
    }
}