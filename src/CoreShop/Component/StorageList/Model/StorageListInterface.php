<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\StorageList\Model;

interface StorageListInterface
{
    /**
     * @return StorageListItemInterface[]
     */
    public function getItems();

    /**
     * @return bool
     */
    public function hasItems();

    /**
     * @param StorageListItemInterface $item
     */
    public function addItem($item);

    /**
     * @param StorageListItemInterface $item
     */
    public function removeItem($item);

    /**
     * @param StorageListItemInterface $item
     *
     * @return bool
     */
    public function hasItem($item);
}
