<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

interface OrderItemFactoryInterface extends FactoryInterface
{
    public function createWithCart(OrderInterface $cart, PurchasableInterface $purchasable, float $quantity = 1.0): OrderItemInterface;

    public function createWithPurchasable(PurchasableInterface $purchasable, float $quantity = 1.0): OrderItemInterface;
}
