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

namespace CoreShop\Bundle\OrderBundle\Factory;

use CoreShop\Bundle\OrderBundle\Controller\AddToCart;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

class AddToCartFactory implements AddToCartFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createWithCartAndPurchasableAndQuantity(CartInterface $cart, PurchasableInterface $purchasable, int $quantity)
    {
        return new AddToCart($cart, $purchasable, $quantity);
    }
}
