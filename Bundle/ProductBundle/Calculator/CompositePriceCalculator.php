<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;

class CompositePriceCalculator implements ProductPriceCalculatorInterface
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $withTax = true)
    {
        $discount = 0;

        foreach ($this->priceRuleCalculators as $calculator) {
            $discount += $calculator->getDiscount($subject, $withTax);
        }

        return $discount;
    }
}
