<?php

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface TaxApplicatorInterface
{
    /**
     * apply taxes on a price.
     *
     * @param $price
     * @param TaxCalculatorInterface $taxCalculator
     * @param bool                   $withTax
     *
     * @return mixed
     */
    public function applyTax($price, TaxCalculatorInterface $taxCalculator, $withTax = true);
}
