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

declare(strict_types=1);

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Model\StorageListInterface;

interface StorageListManagerInterface
{
    /**
     * @return StorageListInterface
     */
    public function getStorageList(): StorageListInterface;

    /**
     * @return bool
     */
    public function hasStorageList(): bool;

    /**
     * @param StorageListInterface $storageList
     */
    public function persist(StorageListInterface $storageList): void;
}
