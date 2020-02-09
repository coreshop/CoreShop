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

namespace CoreShop\Component\Core\Cart;

use CoreShop\Component\Cart\Cart\CartContextResolverInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Webmozart\Assert\Assert;

final class CartContextResolver implements CartContextResolverInterface
{
    /**
     * @var CartContextResolverInterface
     */
    private $inner;

    /**
     * @param CartContextResolverInterface $inner
     */
    public function __construct(CartContextResolverInterface $inner)
    {
        $this->inner = $inner;
    }

    public function resolveCartContext(CartInterface $cart)
    {
        $context = $this->inner->resolveCartContext($cart);

        $store = $cart->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        $context['store'] = $store;
        $context['country'] = $store->getBaseCountry();

        return $context;
    }
}
