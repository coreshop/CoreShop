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

use CoreShop\Component\StorageList\DTO\AddToStorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class AddToStorageListFactory implements AddToStorageListFactoryInterface
{
    /**
     * @psalm-param class-string $addToWishlistClass
     */
    public function __construct(
        protected string $addToWishlistClass,
    ) {
    }

    public function createWithStorageListAndStorageListItem(
        StorageListInterface $storageList,
        StorageListItemInterface $storageListItem,
    ): AddToStorageListInterface {
        $class = new $this->addToWishlistClass($storageList, $storageListItem);

        if (!in_array(AddToStorageListInterface::class, class_implements($class), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s".', $class::class, AddToStorageListInterface::class),
            );
        }

        return $class;
    }
}
