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

namespace CoreShop\Component\StorageList\Factory;

use CoreShop\Component\StorageList\DTO\AddToSelectableStorageList;
use CoreShop\Component\StorageList\DTO\AddToSelectableStorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class AddToSelectableStorageListFactory implements AddToSelectableStorageListFactoryInterface
{
    /**
     * @psalm-param class-string $addToWishlistClass
     */
    public function __construct(
        protected string $addToWishlistClass,
    ) {
    }

    public function createWithStorageListItem(
        StorageListItemInterface $storageListItem,
    ): AddToSelectableStorageList {
        if (!in_array(AddToSelectableStorageListInterface::class, class_implements($this->addToWishlistClass), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s".', $this->addToWishlistClass, AddToSelectableStorageListInterface::class),
            );
        }

        return new $this->addToWishlistClass($storageListItem);
    }
}
