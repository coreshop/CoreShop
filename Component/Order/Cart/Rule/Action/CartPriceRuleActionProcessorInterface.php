<?php

namespace CoreShop\Component\Order\Cart\Rule\Action;

use CoreShop\Component\Order\Model\CartInterface;

interface CartPriceRuleActionProcessorInterface
{
    /**
     * Apply Rule to Cart.
     *
     * @param CartInterface $cart
     * @param $configuration
     *
     * @return bool
     */
    public function applyRule(CartInterface $cart, array $configuration);

    /**
     * Remove Rule from Cart.
     *
     * @param CartInterface $cart
     * @param $configuration
     *
     * @return bool
     */
    public function unApplyRule(CartInterface $cart, array $configuration);

    /**
     * Calculate discount.
     *
     * @param CartInterface $cart
     * @param boolean $withTax
     * @param $configuration
     *
     * @return int
     */
    public function getDiscountCart(CartInterface $cart, $withTax = true, array $configuration);
}
