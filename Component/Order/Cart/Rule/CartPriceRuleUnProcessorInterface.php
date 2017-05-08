<?php

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;

interface CartPriceRuleUnProcessorInterface
{
    /**
     * @param CartPriceRuleInterface $cartPriceRule
     * @param string $usedCode
     * @param CartInterface $cart
     * @return mixed
     */
    public function unProcess(CartPriceRuleInterface $cartPriceRule, $usedCode, CartInterface $cart);
}