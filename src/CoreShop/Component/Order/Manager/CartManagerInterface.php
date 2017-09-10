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
*/

namespace CoreShop\Component\Order\Manager;

use CoreShop\Component\Order\Model\CartInterface;

interface CartManagerInterface
{
    /**
     * @param CartInterface $cart
     *
     * @return mixed
     */
    public function setCurrentCart(CartInterface $cart);

    /**
     * @return CartInterface
     */
    public function getCart();

    /**
     * @return bool
     */
    public function hasCart();

    /**
     * Invalidates current session cart
     */
    public function invalidateSessionCart();

    /**
     * @param CartInterface $cart
     *
     * @return mixed
     */
    public function persistCart(CartInterface $cart);

    /**
     * @param $name
     * @param null $user
     * @param null $store
     * @param null $currency
     * @param bool $persist
     *
     * @return CartInterface
     */
    public function createCart($name, $user = null, $store = null, $currency = null, $persist = false);

    /**
     * @param $user
     *
     * @return CartInterface[]
     */
    public function getStoredCarts($user);

    /**
     * @param $customer
     * @param $name
     *
     * @return CartInterface[]
     */
    public function getByName($customer, $name);

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteCart($id);
}
