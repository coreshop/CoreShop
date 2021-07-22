<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

interface CartItemFactoryInterface extends FactoryInterface
{
    /**
     * @param CartInterface        $cart
     * @param PurchasableInterface $purchasable
     * @param int                  $quantity
     *
     * @return CartItemInterface
     */
    public function createWithCart(CartInterface $cart, PurchasableInterface $purchasable, $quantity = 1);

    /**
     * @param PurchasableInterface $purchasable
     * @param int                  $quantity
     *
     * @return CartItemInterface
     */
    public function createWithPurchasable(PurchasableInterface $purchasable, $quantity = 1);
}
