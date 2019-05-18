<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\DTO;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;

class AddToCart implements AddToCartInterface
{
    /**
     * @var CartInterface
     */
    private $cart;

    /**
     * @var CartItemInterface
     */
    private $cartItem;

    /**
     * @param CartInterface     $cart
     * @param CartItemInterface $cartItem
     */
    public function __construct(CartInterface $cart, CartItemInterface $cartItem)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param CartInterface $cart
     */
    public function setCart(CartInterface $cart)
    {
        $this->cart = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getCartItem()
    {
        return $this->cartItem;
    }

    /**
     * @param CartItemInterface $cartItem
     */
    public function setCartItem(CartItemInterface $cartItem)
    {
        $this->cartItem = $cartItem;
    }
}
