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
     * @param $price
     * @return mixed
     */
    public function getDiscount($subject, $price);
}
