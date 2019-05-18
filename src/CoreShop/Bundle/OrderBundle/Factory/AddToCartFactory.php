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

use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;

class AddToCartFactory implements AddToCartFactoryInterface
{
    /**
     * @var string
     */
    protected $addToCartClass;

    /**
     * @param string $addToCartClass
     */
    public function __construct($addToCartClass)
    {
        $this->addToCartClass = $addToCartClass;
    }

    /**
     * {@inheritdoc}
     */
    public function createWithCartAndCartItem(CartInterface $cart, CartItemInterface $cartItem)
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
