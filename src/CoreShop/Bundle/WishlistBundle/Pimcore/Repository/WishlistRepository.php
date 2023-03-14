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

namespace CoreShop\Bundle\WishlistBundle\Pimcore\Repository;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Repository\WishlistRepositoryInterface;

class WishlistRepository extends PimcoreRepository implements WishlistRepositoryInterface
{
    public function findByStorageListId(int $id): ?StorageListInterface
    {
        return $this->find($id);
    }

    public function findByToken(string $token): ?WishlistInterface
    {
        $list = $this->getList();
        $list->setCondition('token = ?', [$token]);
        $list->load();

        if ($list->getTotalCount() === 1) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    public function findExpiredWishlists(int $days, bool $anonymous, bool $customer): array
    {
        $daysTimestamp = Carbon::now();
        $daysTimestamp->subDays($days);

        $conditions = ['o_modificationDate < ?'];
        $params[] = $daysTimestamp->getTimestamp();
        $groupCondition = [];

        if (true === $anonymous) {
            $groupCondition[] = 'customer__id IS NULL';
        }

        if (true === $customer) {
            $groupCondition[] = 'customer__id IS NOT NULL';
        }

        $bind = ' AND ';
        $groupBind = ' OR ';

        $sql = implode($bind, $conditions);
        $sql .= ' AND (' . implode($groupBind, $groupCondition) . ') ';

        $list = $this->getList();
        $list->setCondition($sql, $params);

        /**
         * @var WishlistInterface[] $result
         */
        $result = $list->getObjects();

        return $result;

    }
}
