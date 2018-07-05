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
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\Model\StorageListProductInterface;

interface StorageListModifierInterface
{
    /**
     * @param StorageListInterface        $storageList
     * @param StorageListProductInterface $product
     * @param int                         $quantity
     *
     * @return mixed
     */
    public function addItem(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 1);

    /**
     * @param StorageListInterface     $storageList
     * @param StorageListItemInterface $item
     *
     * @return mixed
     */
    public function removeItem(StorageListInterface $storageList, StorageListItemInterface $item);

    /**
     * @param StorageListInterface        $storageList
     * @param StorageListProductInterface $product
     * @param int                         $quantity
     * @param bool                        $increaseAmount
     *
     * @return mixed
     */
    public function updateItemQuantity(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 0, $increaseAmount = false);
}
