<?php

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;

class FreeShippingActionProcessor implements CartPriceRuleActionProcessorInterface
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
        return 0;
    }
}
