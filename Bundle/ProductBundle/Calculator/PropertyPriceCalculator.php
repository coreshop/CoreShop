<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceRuleCalculatorInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

class PropertyPriceCalculator implements ProductPriceRuleCalculatorInterface
{
    public function getPrice(RuleSubjectInterface $subject)
    {
        return $subject->getBasePrice();
    }

    public function getDiscount(RuleSubjectInterface $subject, $price, $withTax = true)
    {
        return 0;
    }
}