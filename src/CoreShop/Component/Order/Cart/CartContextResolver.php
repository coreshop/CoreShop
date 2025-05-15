<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\Model\OrderInterface;

final class CartContextResolver implements CartContextResolverInterface
{
    public function resolveCartContext(OrderInterface $cart): array
    {
        return [
            'customer' => $cart->getCustomer() ?: null,
            'currency' => $cart->getCurrency(),
            'base_currency' => $cart->getStore()->getCurrency(),
            'cart' => $cart,
        ];
    }
}
