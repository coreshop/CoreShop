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

namespace CoreShop\Component\Wishlist\Wishlist;

use CoreShop\Component\Wishlist\WishlistEvents;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class WishlistModifier implements WishlistModifierInterface
{
    public function __construct(protected StorageListItemQuantityModifierInterface $wishlistItemQuantityModifier, protected EventDispatcherInterface $eventDispatcher, protected ?\CoreShop\Component\StorageList\StorageListItemResolverInterface $wishlistItemResolver = null)
    {
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
            WishlistEvents::PRE_REMOVE_ITEM
        );

        $storageList->removeItem($item);
        $item->delete();

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $item]),
            WishlistEvents::POST_REMOVE_ITEM
        );
    }

    private function resolveItem(StorageListInterface $storageList, StorageListItemInterface $storageListItem): void
    {
        foreach ($storageList->getItems() as $item) {
            if ($this->wishlistItemResolver->equals($item, $storageListItem)) {
                $this->wishlistItemQuantityModifier->modify(
                    $item,
                    1
                );

                return;
            }
        }

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $storageListItem]),
            WishlistEvents::PRE_ADD_ITEM
        );

        $storageList->addItem($storageListItem);

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $storageListItem]),
            WishlistEvents::POST_ADD_ITEM
        );
    }
}
