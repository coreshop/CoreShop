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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class SimpleStorageListModifier implements StorageListModifierInterface
{
    protected StorageListItemQuantityModifier $storageListItemQuantityModifier;

    protected StorageListItemModelEqualsResolver $storageListItemFinder;

    public function __construct(
        ) {
        $this->storageListItemQuantityModifier = new StorageListItemQuantityModifier();
        $this->storageListItemFinder = new StorageListItemModelEqualsResolver();
    }

    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        $this->resolveItem($storageList, $item);
    }

    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        $storageList->removeItem($item);
    }

    private function resolveItem(StorageListInterface $storageList, StorageListItemInterface $storageListItem): void
    {
        foreach ($storageList->getItems() as $item) {
            if ($this->storageListItemFinder->equals($item, $storageListItem)) {
                $this->storageListItemQuantityModifier->modify(
                    $item,
                    $item->getQuantity() + $storageListItem->getQuantity(),
                );

                return;
            }
        }

        $storageList->addItem($storageListItem);
    }
}
