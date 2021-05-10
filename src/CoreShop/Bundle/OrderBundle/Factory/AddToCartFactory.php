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

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Factory;

use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;

class AddToCartFactory implements AddToCartFactoryInterface
{
    protected string $addToCartClass;

    public function __construct(string $addToCartClass)
    {
        $this->addToCartClass = $addToCartClass;
    }

    public function createWithCartAndCartItem(OrderInterface $cart, OrderItemInterface $cartItem): AddToCartInterface
    {
        $class = new $this->addToCartClass($cart, $cartItem);

        if (!in_array(AddToCartInterface::class, class_implements($class), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s".', get_class($class), AddToCartInterface::class)
            );
        }

        return $class;
    }
}
