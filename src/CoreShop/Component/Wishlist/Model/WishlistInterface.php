<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Wishlist\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\NameableStorageListInterface;
use CoreShop\Component\StorageList\Model\ShareableStorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\TokenAwareStorageListInterface;

interface WishlistInterface extends
    PimcoreModelInterface,
    StorageListInterface,
    TokenAwareStorageListInterface,
    ShareableStorageListInterface,
    NameableStorageListInterface
{
    /**
     * @return WishlistItemInterface[]|null
     */
    public function getItems(): ?array;

    /**
     * @param WishlistItemInterface[] $items
     */
    public function setItems(?array $items);

    public function hasItems(): bool;

    /**
     * @param WishlistItemInterface $item
     */
    public function addItem($item): void;

    /**
     * @param WishlistItemInterface $item
     */
    public function removeItem($item): void;

    /**
     * @param WishlistItemInterface $item
     */
    public function hasItem($item): bool;
}
