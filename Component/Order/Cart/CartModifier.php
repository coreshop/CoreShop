<?php

namespace CoreShop\Component\Order\Cart;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

class CartModifier implements CartModifierInterface
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
    public function addCartItem(CartInterface $cart, ProductInterface $product, $quantity = 1)
    {
        $this->cartManager->persistCart($cart);

        return $this->updateCartItemQuantity($cart, $product, $quantity, true);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCartItem(CartInterface $cart, CartItemInterface $cartItem)
    {
        $cartItem->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function updateCartItemQuantity(CartInterface $cart, ProductInterface $product, $quantity = 0, $increaseAmount = false)
    {
        $item = $cart->getItemForProduct($product);

        if ($item instanceof CartItemInterface) {
            if ($quantity <= 0) {
                $this->removeCartItem($cart, $item);

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
        }
        else {
            /**
             * @var $item CartItemInterface
             */
            $item = $this->cartItemFactory->createNew();
            $item->setKey(uniqid());
            $item->setParent($cart);
            $item->setQuantity($quantity);
            $item->setProduct($product);
            $item->setPublished(true);
            $item->save();

            $cart->addItem($item);
            $cart->save();
        }

        return $item;
    }
}