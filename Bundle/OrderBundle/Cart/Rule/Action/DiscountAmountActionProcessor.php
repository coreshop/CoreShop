<?php

namespace CoreShop\Bundle\OrderBundle\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;

class DiscountAmountActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyRule(CartInterface $cart, array $configuration)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(CartInterface $cart, array $configuration)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(CartInterface $cart, $withTax = true, array $configuration)
    {
        $amount = $configuration['amount'];

        if ($withTax) {
            $subTotalTe = $cart->getSubtotal(false);
            $subTotalTax = $cart->getSubtotalTax();

            if ($subTotalTax > 0) {
                $cartAverageTax = $subTotalTax / $subTotalTe;

                $amount *= 1 + $cartAverageTax;
            }
        }

        return $amount;
    }
}
