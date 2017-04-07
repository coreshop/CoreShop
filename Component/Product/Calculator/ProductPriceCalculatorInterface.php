<?php

namespace CoreShop\Component\Product\Calculator;

interface ProductPriceCalculatorInterface
{
    /**
     * @param $subject
     *
     * @return mixed
     */
    public function getPrice($subject);

    /**
     * @param $subject
     * @param $withTax
     *
     * @return mixed
     */
    public function getDiscount($subject, $withTax = true);
}
