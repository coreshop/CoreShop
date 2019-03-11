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

use CoreShop\Component\Order\Factory\CartItemFactoryInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\Model\StorageListProductInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

class CartModifier implements StorageListModifierInterface
{
    /**
     * @var CartItemFactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param CartItemFactoryInterface $cartItemFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(CartItemFactoryInterface $cartItemFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->cartItemFactory = $cartItemFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function addToStorageList(StorageListInterface $storageList, StorageListItemInterface $item)
    {
        return $this->resolveItem($storageList, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 1)
    {
        /**
         * @var $storageList CartInterface
         * @var $product     PurchasableInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($product, PurchasableInterface::class);

        $this->eventDispatcher->dispatch('coreshop.cart.item_add_pre', new GenericEvent($storageList, ['product' => $product]));

        /**
         * @var StorageListItemInterface $item
         */
        $item = $this->cartItemFactory->createWithCart($storageList, $product, $quantity);
        $item = $this->resolveItem($storageList, $item);

        $this->eventDispatcher->dispatch('coreshop.cart.item_add_post', new GenericEvent($storageList, ['product' => $product]));

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(StorageListInterface $storageList, StorageListItemInterface $item)
    {
        /**
         * @var $storageList CartInterface
         * @var $item        CartItemInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($item, CartItemInterface::class);

        $this->eventDispatcher->dispatch('coreshop.cart.remove_add_pre', new GenericEvent($storageList, ['item' => $item]));

        $storageList->removeItem($item);
        $item->delete();

        $this->eventDispatcher->dispatch('coreshop.cart.remove_add_post', new GenericEvent($storageList, ['item' => $item]));
    }

    /**
     * {@inheritdoc}
     */
    public function updateItemQuantity(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 0, $increaseAmount = false)
    {
        /**
         * @var $storageList CartInterface
         * @var $product     PurchasableInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($product, PurchasableInterface::class);

        $item = $storageList->getItemForProduct($product);

        if ($item instanceof CartItemInterface) {
            $newQuantity = $quantity;

            if ($increaseAmount) {
                $currentQuantity = $item->getQuantity();

                if (is_int($currentQuantity)) {
                    $newQuantity = $currentQuantity + $quantity;
                }
            }

            if ($newQuantity <= 0) {
                $this->removeItem($storageList, $item);

                return false;
            }

            $item->setQuantity($newQuantity);

            return $item;
        }

        return $this->cartItemFactory->createWithCart($storageList, $product, $quantity);
    }

    /**
     * @param StorageListInterface $storageList
     * @param StorageListItemInterface $storageListItem
     */
    private function resolveItem(StorageListInterface $storageList, StorageListItemInterface $storageListItem)
    {
        $item = $storageList->getItemForProduct($storageListItem->getProduct());

        if (null !== $item) {
            $item->setQuantity($item->getQuantity() + $storageListItem->getQuantity());
        }
        else {
            $storageList->addItem($storageListItem);
        }
    }
}
