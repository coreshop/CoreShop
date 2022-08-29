<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\StorageList\DTO;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class AddToStorageList implements AddToStorageListInterface
{
    public function __construct(
        private StorageListInterface $storageList,
        private StorageListItemInterface $storageListItem,
    ) {
    }

    public function getStorageList(): StorageListInterface
    {
        return $this->storageList;
    }

    public function setStorageList(StorageListInterface $storageList): void
    {
        $this->storageList = $storageList;
    }

    public function getStorageListItem(): StorageListItemInterface
    {
        return $this->storageListItem;
    }

    public function setStorageListItem(StorageListItemInterface $storageListItem): void
    {
        $this->storageListItem = $storageListItem;
    }
}
