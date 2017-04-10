<?php

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface TaxCollectorInterface {
    /**
     * @param TaxCalculatorInterface $taxCalculator
     * @param $price
     * @param array $usedTaxes
     * @return mixed
     */
    public function collectTaxes(TaxCalculatorInterface $taxCalculator, $price, $usedTaxes = []);

    /**
     * Merges to Tax arrays from TaxCollector into one
     *
     * @param $taxes1
     * @param $taxes2
     * @return mixed
     */
    public function mergeTaxes($taxes1, $taxes2);
}