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

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\Resource\Factory\FactoryInterface;

final class CartContext implements CartContextInterface
{
    /**
     * @var FactoryInterface
     */
    private $cartFactory;

    /**
     * @param FactoryInterface $cartFactory
     */
    public function __construct(FactoryInterface $cartFactory)
    {
        $this->cartFactory = $cartFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        $cart = $this->cartFactory->createNew();
        $cart->setKey(uniqid());
        $cart->setPublished(true);

        return $cart;
    }
}
