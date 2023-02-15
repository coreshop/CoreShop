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

namespace CoreShop\Component\StorageList\Model;

use Webmozart\Assert\Assert;

abstract class StorageList implements StorageListInterface
{
    protected array $items;

    public function __construct(
        ) {
        $this->items = [];
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems($items): void
    {
        $this->items = $items;
    }

    public function addItem($item): void
    {
        /**
         * @var StorageListItemInterface $item
         */
        Assert::isInstanceOf($item, StorageListItemInterface::class);

        $items = $this->getItems();
        $item->setStorageList($this);

        $items[] = $item;

        $this->setItems($items);
    }

    public function removeItem($item): void
    {
        Assert::isInstanceOf($item, StorageListItemInterface::class);

        $items = $this->getItems();

        foreach ($items as $i => $iValue) {
            $arrayItem = $iValue;

            if ($arrayItem->getId() === $item->getId()) {
                unset($items[$i]);

                break;
            }
        }

        $this->setItems(array_values($items));
    }

    public function hasItem($item): bool
    {
        Assert::isInstanceOf($item, StorageListItemInterface::class);

        $items = $this->getItems();

        foreach ($items as $iValue) {
            $arrayItem = $iValue;

            if ($arrayItem->getId() === $item->getId()) {
                return true;
            }
        }

        return false;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }
}
