<?php

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

final class TaxApplicator implements TaxApplicatorInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        StoreContextInterface $storeContext
    ) {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTax($price, TaxCalculatorInterface $taxCalculator, $withTax = true)
    {
        $useGrossPrice = $this->storeContext->getStore()->getUseGrossPrice();

        if ($useGrossPrice) {
            if ($withTax) {
                return $price;
            }

            return $taxCalculator->removeTaxes($price);
        }

        if ($withTax) {
            return $taxCalculator->applyTaxes($price);
        }

        return $price;
    }
}
