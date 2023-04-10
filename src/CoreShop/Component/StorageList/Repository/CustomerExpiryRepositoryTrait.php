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

namespace CoreShop\Component\StorageList\Repository;

use Carbon\Carbon;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use Pimcore\Model\DataObject\Listing;

trait CustomerExpiryRepositoryTrait
{
    public function findExpiredStorageLists(int $days, array $params = []): array
    {
        $daysTimestamp = Carbon::now();
        $daysTimestamp->subDays($days);

        $conditions = ['o_modificationDate < ?'];
        $queryParams = [$daysTimestamp->getTimestamp()];
        $groupCondition = [];

        if (true === $params['anonymous'] ?? false) {
            $groupCondition[] = 'customer__id IS NULL';
        }

        if (true === $params['customer'] ?? false) {
            $groupCondition[] = 'customer__id IS NOT NULL';
        }

        $bind = ' AND ';
        $groupBind = ' OR ';

        $sql = implode($bind, $conditions);
        $sql .= ' AND (' . implode($groupBind, $groupCondition) . ') ';

        $list = $this->getList();
        $list->setCondition($sql, $queryParams);

        /**
         * @var StorageListInterface[] $result
         */
        $result = $list->getObjects();

        return $result;
    }

    /**
     * @return Listing
     */
    abstract public function getList();
}
