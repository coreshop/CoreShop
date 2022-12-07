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

use CoreShop\Component\StorageList\DTO\AddToNewStorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class AddToNewStorageListFactory implements AddToNewStorageListFactoryInterface
{
    /**
     * @psalm-param class-string $addToNewStorageListClass
     */
    public function __construct(
        protected string $addToNewStorageListClass,
    ) {
    }

    public function createWithStorageListItem(
        StorageListItemInterface $storageListItem,
    ): AddToNewStorageListInterface {
        if (!in_array(AddToNewStorageListInterface::class, class_implements($this->addToNewStorageListClass), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s".',
                    $this->addToNewStorageListClass,
                    AddToNewStorageListInterface::class
                ),
            );
        }

        return new $this->addToNewStorageListClass($storageListItem);
    }
}
