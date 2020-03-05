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

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Resource\Factory\FactoryInterface;

class OrderFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $cartFactory;

    public function __construct(FactoryInterface $cartFactory)
    {
        $this->cartFactory = $cartFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $cart = $this->cartFactory->createNew();
        $cart->setKey(uniqid('cart', true));
        $cart->setPublished(true);

        return $cart;
    }
}
