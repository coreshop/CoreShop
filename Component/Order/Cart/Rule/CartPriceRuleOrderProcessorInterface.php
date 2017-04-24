<?php

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;

interface CartPriceRuleOrderProcessorInterface
{
    /**
     * @param CartPriceRuleInterface $cartPriceRule
     * @param $usedCode
     * @param CartInterface $cart
     * @param OrderInterface $order
     * @return mixed
     */
    public function process(CartPriceRuleInterface $cartPriceRule, $usedCode, CartInterface $cart, OrderInterface $order);
}