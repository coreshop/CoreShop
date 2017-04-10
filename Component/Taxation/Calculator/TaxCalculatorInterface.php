<?php

namespace CoreShop\Component\Taxation\Calculator;

use CoreShop\Component\Taxation\Model\TaxRateInterface;

interface TaxCalculatorInterface {
    /**
     * DISABLE_METHOD only use this tax.
     */
    const DISABLE_METHOD = 0;

    /**
     * COMBINE_METHOD sum taxes
     * eg: 100€ * (10% + 15%).
     */
    const COMBINE_METHOD = 1;

    /**
     * ONE_AFTER_ANOTHER_METHOD apply taxes one after another
     * eg: (100€ * 10%) * 15%.
     */
    const ONE_AFTER_ANOTHER_METHOD = 2;

    /**
     * Compute and add the taxes to the specified price.
     *
     * @param $price
     * @return float
     */
    public function applyTaxes($price);

    /**
     * Compute and remove the taxes to the specified price.
     *
     * @param $price
     * @return float
     */
    public function removeTaxes($price);

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator.
     *
     * @param float $price
     * @param bool  $asArray
     *
     * @return float|array $taxes_amount
     */
    public function getTaxesAmount($price, $asArray = false);

    /**
     * Compute Total Rate.
     *
     * @return float
     */
    public function getTotalRate();

    /**
     * @return TaxRateInterface[]
     */
    public function getTaxRates();
}