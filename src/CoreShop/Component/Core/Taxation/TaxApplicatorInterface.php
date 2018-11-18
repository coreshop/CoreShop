<?php

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface TaxApplicatorInterface
{
    /**
     * apply taxes on a price
     *
     * @param int $price
     * @param array $context
     * @param TaxCalculatorInterface $taxCalculator
     * @param bool $withTax
     * @return mixed
     */
    public function applyTax($price, array $context, TaxCalculatorInterface $taxCalculator, $withTax = true);
}