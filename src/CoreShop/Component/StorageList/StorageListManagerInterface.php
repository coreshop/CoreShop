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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Model\StorageListInterface;

interface StorageListManagerInterface
{
    /**
     * @return StorageListInterface
     */
    public function getStorageList();

    /**
     * @return bool
     */
    public function hasStorageList();

    /**
     * @param StorageListInterface $storageList
     *
     * @return bool
     */
    public function persist(StorageListInterface $storageList);
}
