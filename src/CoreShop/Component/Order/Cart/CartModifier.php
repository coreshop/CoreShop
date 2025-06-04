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

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\CartEvents;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class CartModifier implements CartModifierInterface
{
    public function __construct(
        protected StorageListItemQuantityModifierInterface $cartItemQuantityModifier,
        protected EventDispatcherInterface $eventDispatcher,
        protected ?\CoreShop\Component\StorageList\StorageListItemResolverInterface $cartItemResolver = null,
    ) {
    }

    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        $this->resolveItem($storageList, $item);
    }

    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        /**
         * @var OrderInterface $storageList
         * @var OrderItemInterface $item
         */
        Assert::isInstanceOf($storageList, OrderInterface::class);
        Assert::isInstanceOf($item, OrderItemInterface::class);

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $item]),
            CartEvents::PRE_REMOVE_ITEM,
        );

        $storageList->removeItem($item);
        $item->delete();

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $item]),
            CartEvents::POST_REMOVE_ITEM,
        );
    }

    private function resolveItem(StorageListInterface $storageList, StorageListItemInterface $storageListItem): void
    {
        foreach ($storageList->getItems() as $item) {
            if ($this->cartItemResolver->equals($item, $storageListItem)) {
                $this->cartItemQuantityModifier->modify(
                    $item,
                    $item->getQuantity() + $storageListItem->getQuantity(),
                );

                return;
            }
        }

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $storageListItem]),
            CartEvents::PRE_ADD_ITEM,
        );

        $storageList->addItem($storageListItem);

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $storageListItem]),
            CartEvents::POST_ADD_ITEM,
        );
    }
}
