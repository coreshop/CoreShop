<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
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
     * @var FactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param FactoryInterface         $cartItemFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $cartItemFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->cartItemFactory = $cartItemFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 1)
    {
        /*
         * @var $storageList CartInterface
         * @var $product PurchasableInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($product, PurchasableInterface::class);

        $this->eventDispatcher->dispatch('coreshop.cart.item_add_pre', new GenericEvent($storageList, ['product' => $product]));

        $result = $this->updateItemQuantity($storageList, $product, $quantity, true);

        $this->eventDispatcher->dispatch('coreshop.cart.item_add_post', new GenericEvent($storageList, ['product' => $product]));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(StorageListInterface $storageList, StorageListItemInterface $item)
    {
        /*
         * @var $storageList CartInterface
         * @var $item CartItemInterface
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
        /*
         * @var $storageList CartInterface
         * @var $product PurchasableInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($product, PurchasableInterface::class);

        $item = $storageList->getItemForProduct($product);

        if ($item instanceof CartItemInterface) {
            if ($quantity <= 0) {
                $this->removeItem($storageList, $item);

                return false;
            }

            $newQuantity = $quantity;

            if ($increaseAmount) {
                $currentQuantity = $item->getQuantity();

                if (is_int($currentQuantity)) {
                    $newQuantity = $currentQuantity + $quantity;
                }
            }

            $item->setQuantity($newQuantity);
        } else {
            /**
             * @var CartItemInterface
             */
            $item = $this->cartItemFactory->createNew();
            $item->setKey(uniqid());
            $item->setParent($storageList);
            $item->setQuantity($quantity);
            $item->setProduct($product);
            $item->setPublished(true);

            $storageList->addItem($item);
        }

        return $item;
    }
}
