<?php

namespace CoreShop\Component\Order\Cart\Calculator;

interface CartDiscountCalculatorInterface
{
    /**
     * @param $subject
     * @param bool $withTax
     * @return mixed
     */
    public function getDiscount($subject, $withTax = true);
}
