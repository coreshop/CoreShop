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
        protected string $addToWishlistClass
    ) {
    }

    public function createWithStorageListAndStorageListItem(
        StorageListInterface $storageList,
        StorageListItemInterface $storageListItem
    ): AddToStorageListInterface {
        $class = new $this->addToWishlistClass($storageList, $storageListItem);

        if (!in_array(AddToStorageListInterface::class, class_implements($class), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s".', $class::class, AddToStorageListInterface::class)
            );
        }

        return $class;
    }
}
