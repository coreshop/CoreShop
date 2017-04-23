<?php

namespace CoreShop\Bundle\OrderBundle\Cart\Calculator;

use CoreShop\Component\Order\Cart\Calculator\CartDiscountCalculatorInterface;

class CompositeCartDiscountCalculator implements CartDiscountCalculatorInterface
{
    /**
     * @var CartDiscountCalculatorInterface[]
     */
    protected $cartDiscountCalculators;

    /**
     * @param CartDiscountCalculatorInterface[] $cartDiscountCalculators
     */
    public function __construct(array $cartDiscountCalculators)
    {
        $this->cartDiscountCalculators = $cartDiscountCalculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $withTax = true)
    {
        $discount = 0;

        foreach ($this->cartDiscountCalculators as $calculator) {
            $discount += $calculator->getDiscount($subject, $withTax);
        }

        return $discount;
    }
}
