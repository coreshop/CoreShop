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

namespace CoreShop\Component\Wishlist\Wishlist;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use CoreShop\Component\Wishlist\WishlistEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class WishlistModifier implements StorageListModifierInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected StorageListItemResolverInterface $wishlistItemResolver,
    ) {
    }

    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        $this->resolveItem($storageList, $item);
    }

    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        /**
         * @var WishlistInterface $storageList
         * @var WishlistItemInterface $item
         */
        Assert::isInstanceOf($storageList, WishlistInterface::class);
        Assert::isInstanceOf($item, WishlistItemInterface::class);

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $item]),
            WishlistEvents::PRE_REMOVE_ITEM,
        );

        $storageList->removeItem($item);
        $item->delete();

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $item]),
            WishlistEvents::POST_REMOVE_ITEM,
        );
    }

    private function resolveItem(StorageListInterface $storageList, StorageListItemInterface $storageListItem): void
    {
        foreach ($storageList->getItems() as $item) {
            if ($this->wishlistItemResolver->equals($item, $storageListItem)) {
                return;
            }
        }

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $storageListItem]),
            WishlistEvents::PRE_ADD_ITEM,
        );

        $storageList->addItem($storageListItem);

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $storageListItem]),
            WishlistEvents::POST_ADD_ITEM,
        );
    }
}
