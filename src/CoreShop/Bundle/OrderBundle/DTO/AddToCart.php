<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\DTO;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;

class AddToCart implements AddToCartInterface
{
    private $cart;
    private $cartItem;

    public function __construct(CartInterface $cart, CartItemInterface $cartItem)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart(): CartInterface
    {
        return $this->cart;
    }

    /**
     * @param CartInterface $cart
     */
    public function setCart(CartInterface $cart): void
    {
        $this->cart = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getCartItem(): CartItemInterface
    {
        return $this->cartItem;
    }

    /**
     * @param CartItemInterface $cartItem
     */
    public function setCartItem(CartItemInterface $cartItem): void
    {
        $this->cartItem = $cartItem;
    }
}
