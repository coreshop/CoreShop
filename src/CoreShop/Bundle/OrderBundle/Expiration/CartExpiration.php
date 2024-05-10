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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Expiration;

use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\StorageList\Expiration\StorageListExpirationInterface;

final class CartExpiration implements StorageListExpirationInterface
{
    public function __construct(
        private OrderRepositoryInterface $cartRepository,
    ) {
    }

    public function expire(int $days, array $params = []): void
    {
        if ($days <= 0) {
            return;
        }

        $carts = $this->cartRepository->findExpiredCarts($days, $params['anonymous'], $params['customer']);

        foreach ($carts as $cart) {
            $cart->delete();
        }
    }
}
