<?php

namespace CoreShop\Component\Order\Taxation;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Webmozart\Assert\Assert;

class CartTaxCollector implements ProposalTaxCollectorInterface
{
    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @var ProposalItemTaxCollectorInterface
     */
    private $cartItemTaxCollector;

    /**
     * @param TaxCollectorInterface $taxCollector
     * @param ProposalItemTaxCollectorInterface $cartItemTaxCollector
     */
    public function __construct(
        TaxCollectorInterface $taxCollector,
        ProposalItemTaxCollectorInterface $cartItemTaxCollector
    )
    {
        $this->taxCollector = $taxCollector;
        $this->cartItemTaxCollector = $cartItemTaxCollector;
    }

    /**
     * @param ProposalInterface $cart
     * @return TaxItemInterface[]
     */
    public function getTaxes(ProposalInterface $cart)
    {
        /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);

        $usedTaxes = [];

        foreach ($cart->getItems() as $item) {
            $usedTaxes = $this->taxCollector->mergeTaxes($this->cartItemTaxCollector->getTaxes($item), $usedTaxes);
        }

        return $usedTaxes;
    }
}