<?php

namespace CoreShop\Bundle\TrackingBundle\Builder;

use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Model\Object\Fieldcollection;

class ItemBuilder implements ItemBuilderInterface
{
    /**
     * Build a product view object
     *
     * @param PurchasableInterface $product
     * @return ProductData
     */
    public function buildPurchasableViewItem(PurchasableInterface $product)
    {
        return $this->buildPurchasableActionItem($product);
    }

    /**
     * Build a product action item object
     *
     * @param PurchasableInterface $product
     * @param int $quantity
     * @return ProductData
     */
    public function buildPurchasableActionItem(PurchasableInterface $product, $quantity = 1)
    {
        $item = new ProductData();

        $item->setId($product->getId());
        $item->setName($product->getName());
        $item->setCategory($product->getCategories()[0]->getName());
        $item->setQuantity($quantity);
        $item->setPrice($product->getPrice(true));

        return $item;
    }

    /**
     * Build a product impression object
     *
     * @param PurchasableInterface $product
     * @return ImpressionData
     */
    public function buildPurchasableImpressionItem(PurchasableInterface $product)
    {
        $item = new ImpressionData();
        $item->setId($product->getId());
        $item->setName($product->getName());
        $item->setCategory($product->getCategories()[0]->getName());
        $item->setPrice($product->getPrice(true));

        return $item;
    }

    /**
     * Build a checkout transaction object
     *
     * @param OrderInterface $order
     * @return ActionData
     */
    public function buildOrderAction(OrderInterface $order)
    {
        $item = new ActionData();
        $item->setId($order->getOrderNumber());
        $item->setRevenue($order->getTotal());
        $item->setShipping($order->getShipping());
        $item->setTax($order->getTotalTax());
        $item->setAffiliation($order->getStore()->getName());

        if ($order->getPriceRuleItems() instanceof Fieldcollection) {
            if ($order->getPriceRuleItems()->getCount() > 0) {
                foreach ($order->getPriceRuleItems() as $priceRule) {
                    if ($priceRule instanceof ProposalCartPriceRuleItemInterface) {
                        if ($priceRule->getPriceRule() instanceof CartPriceRuleInterface) {
                            $item->setCoupon($priceRule->getPriceRule()->getName());
                        }
                    }
                }
            }
        }

        return $item;
    }

    /**
     * Build checkout items
     *
     * @param OrderInterface $order
     * @return ProductData[]
     */
    public function buildCheckoutItems(OrderInterface $order)
    {
        $items = [];

        foreach ($order->getItems() as $item) {
            $items[] = $this->buildCheckoutItem($order, $item);
        }

        return $items;
    }

    /**
     * Build checkout items by cart
     *
     * @param CartInterface $cart
     * @return mixed
     */
    public function buildCheckoutItemsByCart(CartInterface $cart)
    {
        $items = [];

        foreach ($cart->getItems() as $item) {
            $items[] = $this->buildPurchasableActionItem($item->getProduct(), $item->getQuantity());
        }

        return $items;
    }

    /**
     * Build a checkout item object
     *
     * @param OrderInterface $order
     * @param OrderItemInterface $orderItem
     * @return ProductData
     */
    public function buildCheckoutItem(OrderInterface $order, OrderItemInterface $orderItem)
    {
        $item = new ProductData();
        $item->setId($orderItem->getId());
        $item->setName($orderItem->getProduct()->getName());
        $item->setCategory($orderItem->getProduct()->getCategories()[0]->getName());
        $item->setPrice($orderItem->getItemPrice());
        $item->setQuantity($orderItem->getQuantity());

        return $item;
    }
}
