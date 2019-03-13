<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\StorageList\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface StorageListItemInterface extends ResourceInterface
{
    /**
     * @param StorageListItemInterface $storageListItem
     * @return bool
     */
    public function equals(StorageListItemInterface $storageListItem);

    /**
     * @return mixed
     */
    public function getProduct();

    /**
     * @param mixed
     */
    public function setProduct($product);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity);
}
