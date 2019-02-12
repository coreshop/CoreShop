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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

final class AddToCartCommand implements AddToCartCommandInterface
{
    /**
     * @var CartInterface
     */
    private $cart;

    /**
     * @var PurchasableInterface
     */
    private $purchasable;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param CartInterface        $cart
     * @param PurchasableInterface $purchasable
     * @param int                  $quantity
     */
    public function __construct(CartInterface $cart, PurchasableInterface $purchasable, int $quantity)
    {
        $this->cart = $cart;
        $this->purchasable = $purchasable;
        $this->quantity = $quantity;
    }

    /**
     * @return CartInterface
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
     * @return PurchasableInterface
     */
    public function getPurchasable()
    {
        return $this->purchasable;
    }

    /**
     * @param PurchasableInterface $purchasable
     */
    public function setPurchasable(PurchasableInterface $purchasable)
    {
        $this->purchasable = $purchasable;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }
}
