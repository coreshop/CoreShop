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

namespace CoreShop\Bundle\StorageListBundle\Expiration;

use CoreShop\Bundle\CoreBundle\Pimcore\Repository\WishlistRepository;
use CoreShop\Bundle\OrderBundle\Expiration\OrderExpirationInterface;

final class WishlistExpiration implements OrderExpirationInterface
{
    public function __construct(
        private WishlistRepository $wishlistRepository,
    ) {
    }

    public function expire(int $days, array $params = []): void
    {
        if ($days <= 0) {
            return;
        }

        $wishlists = $this->wishlistRepository->findExpiredItems($days, $params['anonymous'], $params['customer']);

        foreach ($wishlists as $wishlist) {
            $wishlist->delete();
        }
    }
}
