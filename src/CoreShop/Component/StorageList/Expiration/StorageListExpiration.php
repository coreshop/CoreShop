<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\StorageList\Expiration;

use CoreShop\Component\StorageList\Repository\ExpireAbleStorageListRepositoryInterface;

final class StorageListExpiration implements StorageListExpirationInterface
{
    public function __construct(
        private ExpireAbleStorageListRepositoryInterface $repository,
    ) {
    }

    public function expire(int $days, array $params = []): void
    {
        if ($days <= 0) {
            return;
        }

        $lists = $this->repository->findExpiredStorageLists($days, $params);

        foreach ($lists as $list) {
            $list->delete();
        }
    }
}
