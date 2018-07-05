<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\CartInterface;

interface CheckoutManagerFactoryInterface
{
    /**
     * @param CartInterface $cart
     *
     * @return CheckoutManagerInterface
     */
    public function createCheckoutManager(CartInterface $cart);
}
