<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceRuleCalculatorInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

class CompositePriceCalculator implements ProductPriceRuleCalculatorInterface
{
    /**
     * @var ProductPriceRuleCalculatorInterface[]
     */
    protected $priceRuleCalculators;

    /**
     * @param ProductPriceRuleCalculatorInterface[] $priceRuleCalculators
     */
    public function __construct(array $priceRuleCalculators)
    {
        $this->priceRuleCalculators = $priceRuleCalculators;
    }

    public function getPrice(RuleSubjectInterface $subject)
    {
        $price = false;

        foreach ($this->priceRuleCalculators as $calculator) {
            $actionPrice = $calculator->getPrice($subject);

            if (false !== $actionPrice && null !== $actionPrice) {
                $price = $actionPrice;
            }
        }

        return $price;
    }

    public function getDiscount(RuleSubjectInterface $subject, $price, $withTax = true)
    {
        $discount = 0;

        foreach ($this->priceRuleCalculators as $calculator) {
            $discount += $calculator->getDiscount($subject, $price, $withTax);
        }

        return $discount;
    }

}