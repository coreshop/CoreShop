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

use CoreShop\Component\StorageList\Expiration\StorageListExpirationInterface;

final class OrderAndCartExpiration implements StorageListExpirationInterface
{
    public function __construct(
        private OrderExpiration $orderExpiration,
        private CartExpiration $cartExpiration,
    ) {
    }

    public function expire(int $days, array $params = []): void
    {
        $this->orderExpiration->expire($params['order']['days'], $params['order']['params'] ?? []);
        $this->cartExpiration->expire($params['cart']['days'], $params['cart']['params'] ?? []);
    }
}
