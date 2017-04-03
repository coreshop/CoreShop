<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

class PropertyPriceCalculatorInterface implements ProductPriceCalculatorInterface
{
    public function getPrice($subject)
    {
        return $subject->getBasePrice();
    }

    public function getDiscount($subject, $price, $withTax = true)
    {
        return 0;
    }
}