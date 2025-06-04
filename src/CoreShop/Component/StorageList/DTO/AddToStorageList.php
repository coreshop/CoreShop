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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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
