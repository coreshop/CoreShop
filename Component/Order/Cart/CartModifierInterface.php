<?php

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;

interface CartModifierInterface
{
    /**
     * @param CartInterface $cart
     * @param ProductInterface $product
     * @param int $quantity
     * @return mixed
     */
    public function addCartItem(CartInterface $cart, ProductInterface $product, $quantity = 1);

    /**
     * @param CartInterface $cart
     * @param CartItemInterface $cartItem
     * @return mixed
     */
    public function removeCartItem(CartInterface $cart, CartItemInterface $cartItem);

    /**
     * @param CartInterface $cart
     * @param ProductInterface $product
     * @param int $quantity
     * @param bool $increaseAmount
     * @return mixed
     */
    public function updateCartItemQuantity(CartInterface $cart, ProductInterface $product, $quantity = 0, $increaseAmount = false);
}