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

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\Model\StorageListProductInterface;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Webmozart\Assert\Assert;

class CartModifier implements CartModifierInterface, StorageListModifierInterface
{
    /**
     * @var CartManagerInterface
     */
    protected $cartManager;

    /**
     * @var FactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @param CartManagerInterface $cartManager
     * @param FactoryInterface $cartItemFactory
     */
    public function __construct(CartManagerInterface $cartManager, FactoryInterface $cartItemFactory)
    {
        $this->cartManager = $cartManager;
        $this->cartItemFactory = $cartItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 1)
    {
        /**
         * @var $storageList CartInterface
         * @var $product PurchasableInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($product, PurchasableInterface::class);

        $this->cartManager->persistCart($storageList);

        return $this->updateItemQuantity($storageList, $product, $quantity, true);
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(StorageListInterface $storageList, StorageListItemInterface $item)
    {
        /**
         * @var $storageList CartInterface
         * @var $item CartItemInterface
         */
        Assert::isInstanceOf($storageList, CartInterface::class);
        Assert::isInstanceOf($item, CartItemInterface::class);

        $item->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function updateItemQuantity(StorageListInterface $storageList, StorageListProductInterface $product, $quantity = 0, $increaseAmount = false)
    {
        /**
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
            $item->save();
        } else {
            /**
             * @var CartItemInterface
             */
            $item = $this->cartItemFactory->createNew();
            $item->setKey(uniqid());
            $item->setParent($storageList);
            $item->setQuantity($quantity);
            $item->setProduct($product);
            $item->setDigitalProduct($product->getDigitalProduct());
            $item->setPublished(true);
            $item->save();

            $storageList->addItem($item);
            $storageList->save();
        }

        return $item;
    }

    /**
     * @deprecated Use addItem instead, will be removed in 2.0.0-Alpha-3
     *
     * {@inheritdoc}
     */
    public function addCartItem(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        $this->cartManager->persistCart($cart);

        return $this->addItem($cart, $product, $quantity);
    }

    /**
     * @deprecated Use removeItem instead, will be removed in 2.0.0-Alpha-3
     *
     * {@inheritdoc}
     */
    public function removeCartItem(CartInterface $cart, CartItemInterface $cartItem)
    {
        return $this->removeItem($cart, $cartItem);
    }

    /**
     * @deprecated Use updateItemQuantity instead, will be removed in 2.0.0-Alpha-3
     *
     * {@inheritdoc}
     */
    public function updateCartItemQuantity(CartInterface $cart, PurchasableInterface $product, $quantity = 0, $increaseAmount = false)
    {
        return $this->updateItemQuantity($cart, $product, $quantity, $increaseAmount);
    }
}
