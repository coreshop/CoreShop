<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;

class PropertyPriceCalculator implements ProductPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice($subject)
    {
        return $subject->getBasePrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $withTax = true)
    {
        return 0;
    }
}
