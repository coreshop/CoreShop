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

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

class CartModifier implements StorageListModifierInterface
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

        if (null === $cartItemResolver) {
            @trigger_error(
                'Not passing a StorageListItemResolverInterface as third argument is deprecated since 2.1.1 and will be removed with 3.0.0',
                E_USER_DEPRECATED
            );

            $this->cartItemResolver = new CartItemResolver();
        }
        else {
            $this->cartItemResolver = $cartItemResolver;
        }
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
