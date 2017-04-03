<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;

class CompositePriceCalculatorInterface implements ProductPriceCalculatorInterface
{
    /**
     * @var ProductPriceCalculatorInterface[]
     */
    protected $priceRuleCalculators;

    /**
     * @param ProductPriceCalculatorInterface[] $priceRuleCalculators
     */
    public function __construct(array $priceRuleCalculators)
    {
        $this->priceRuleCalculators = $priceRuleCalculators;
    }

    public function getPrice($subject)
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

    public function getDiscount($subject, $price, $withTax = true)
    {
        $discount = 0;

        foreach ($this->priceRuleCalculators as $calculator) {
            $discount += $calculator->getDiscount($subject, $price, $withTax);
        }

        return $discount;
    }

}