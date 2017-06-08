<?php

namespace CoreShop\Bundle\CoreBundle\Tracking\Builder;

use CoreShop\Bundle\TrackingBundle\Builder\ItemBuilderInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Model\ProductInterface;

final class ItemBuilder implements ItemBuilderInterface
{
    /**
     * @var ItemBuilderInterface
     */
    protected $decoratedItemBuilder;

    /**
     * @param ItemBuilderInterface $decoratedItemBuilder
     */
    public function __construct(ItemBuilderInterface $decoratedItemBuilder)
    {
        $this->decoratedItemBuilder = $decoratedItemBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPurchasableActionItem(PurchasableInterface $product, $quantity = 1)
    {
        $item = $this->decoratedItemBuilder->buildPurchasableActionItem($product, $quantity);

        if ($product instanceof ProductInterface) {
            if (count($product->getCategories()) > 0) {
                $item->setCategory($product->getCategories()[0]->getName());
            }
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPurchasableImpressionItem(PurchasableInterface $product)
    {
        $item = $this->decoratedItemBuilder->buildPurchasableImpressionItem($product);

        if ($product instanceof ProductInterface) {
            if (count($product->getCategories()) > 0) {
                $item->setCategory($product->getCategories()[0]->getName());
            }
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOrderAction(OrderInterface $order)
    {
        $item = $this->decoratedItemBuilder->buildOrderAction($order);

        if ($order instanceof \CoreShop\Component\Core\Model\OrderInterface) {
            $item->setAffiliation($order->getStore()->getName());
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPurchasableViewItem(PurchasableInterface $product)
    {
        return $this->buildPurchasableActionItem($product);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCheckoutItems(OrderInterface $order)
    {
        return $this->decoratedItemBuilder->buildCheckoutItems($order);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCheckoutItemsByCart(CartInterface $cart)
    {
        return $this->decoratedItemBuilder->buildCheckoutItemsByCart($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCheckoutItem(OrderInterface $order, OrderItemInterface $orderItem)
    {
        $item = $this->decoratedItemBuilder->buildCheckoutItem($order, $orderItem);
        
        if ($orderItem->getProduct() instanceof ProductInterface) {
            if (count($orderItem->getProduct()->getCategories()) > 0) {
                $item->setCategory($orderItem->getProduct()->getCategories()[0]->getName());
            }
        }

        return $item;
    }
}
