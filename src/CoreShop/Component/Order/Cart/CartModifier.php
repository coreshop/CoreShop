<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

class CartModifier implements CartModifierInterface
{
    protected $cartItemQuantityModifier;
    protected $eventDispatcher;
    protected $cartItemResolver;

    public function __construct(
        StorageListItemQuantityModifierInterface $cartItemQuantityModifier,
        EventDispatcherInterface $eventDispatcher,
        StorageListItemResolverInterface $cartItemResolver = null
    ) {
        $this->cartItemQuantityModifier = $cartItemQuantityModifier;
        $this->eventDispatcher = $eventDispatcher;
        $this->cartItemResolver = $cartItemResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        $this->resolveItem($storageList, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item): void
    {
        /**
         * @var $storageList OrderInterface
         * @var $item        OrderItemInterface
         */
        Assert::isInstanceOf($storageList, OrderInterface::class);
        Assert::isInstanceOf($item, OrderItemInterface::class);

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $item]),
            'coreshop.cart.remove_add_pre'
        );

        $storageList->removeItem($item);
        $item->delete();

        $this->eventDispatcher->dispatch(
            new GenericEvent($storageList, ['item' => $item]),
            'coreshop.cart.remove_add_post'
        );
    }

    /**
     * @param StorageListInterface     $storageList
     * @param StorageListItemInterface $storageListItem
     */
    private function resolveItem(StorageListInterface $storageList, StorageListItemInterface $storageListItem): void
    {
        foreach ($storageList->getItems() as $item) {
            if ($this->cartItemResolver->equals($item, $storageListItem)) {
                $this->cartItemQuantityModifier->modify(
                    $item,
                    $item->getQuantity() + $storageListItem->getQuantity()
                );

                return;
            }
        }

        $storageList->addItem($storageListItem);
    }
}
