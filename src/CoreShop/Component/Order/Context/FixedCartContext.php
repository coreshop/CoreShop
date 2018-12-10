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

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\Order\Model\CartInterface;

final class FixedCartContext implements CartContextInterface
{
    /**
     * @var CartInterface
     */
    private $cart = null;

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if ($this->cart instanceof CartInterface) {
            return $this->cart;
        }

        throw new CartNotFoundException();
    }

    /**
     * @param CartInterface $cart
     */
    public function setCart(CartInterface $cart)
    {
        $this->cart = $cart;
    }
}
