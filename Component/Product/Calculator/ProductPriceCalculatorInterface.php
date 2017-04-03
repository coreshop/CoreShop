<?php

namespace CoreShop\Component\Product\Calculator;

interface ProductPriceCalculatorInterface
{
    /**
     * @param $subject
     * @return mixed
     */
    public function getPrice($subject);

    /**
     * @param $subject
     * @param $price
     * @param bool $withTax
     * @return mixed
     */
    public function getDiscount($subject, $price, $withTax = true);
}