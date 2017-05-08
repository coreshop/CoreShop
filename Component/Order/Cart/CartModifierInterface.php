<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

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