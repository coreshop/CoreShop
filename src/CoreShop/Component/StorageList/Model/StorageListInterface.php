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

namespace CoreShop\Component\StorageList\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface StorageListInterface extends ResourceInterface
{
    /**
     * @param StorageListProductInterface $product
     *
     * @return StorageListItemInterface|null
     */
    public function getItemForProduct(StorageListProductInterface $product);

    /**
     * @return StorageListItemInterface[]
     */
    public function getItems();

    /**
     * @return bool
     */
    public function hasItems();

    /**
     * @param $item
     */
    public function addItem($item);

    /**
     * @param $item
     */
    public function removeItem($item);

    /**
     * @param $item
     *
     * @return bool
     */
    public function hasItem($item);
}
