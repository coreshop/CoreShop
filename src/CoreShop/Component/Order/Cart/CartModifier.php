<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

class CartModifier implements StorageListModifierInterface
{
    /**
     * @var StorageListItemQuantityModifierInterface
     */
    protected $cartItemQuantityModifier;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param StorageListItemQuantityModifierInterface $cartItemQuantityModifier
     * @param EventDispatcherInterface                 $eventDispatcher
     */
    public function __construct(
        StorageListItemQuantityModifierInterface $cartItemQuantityModifier,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->cartItemQuantityModifier = $cartItemQuantityModifier;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function addToList(StorageListInterface $storageList, StorageListItemInterface $item)
    {
        return $this->resolveItem($storageList, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFromList(StorageListInterface $storageList, StorageListItemInterface $item)
    {
        /**
         * @var $storageList CartInterface
         * @var $item        CartItemInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($item, CartItemInterface::class);

        $this->eventDispatcher->dispatch(
            'coreshop.cart.remove_add_pre',
        new GenericEvent($storageList, ['item' => $item])
            );

        $storageList->removeItem($item);
        $item->delete();

        $this->eventDispatcher->dispatch(
            'coreshop.cart.remove_add_post',
        new GenericEvent($storageList, ['item' => $item])
            );
    }

    /**
     * @param StorageListInterface     $storageList
     * @param StorageListItemInterface $storageListItem
     */
    private function resolveItem(StorageListInterface $storageList, StorageListItemInterface $storageListItem)
    {
        foreach ($storageList->getItems() as $item) {
            if ($storageListItem->equals($item)) {
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
