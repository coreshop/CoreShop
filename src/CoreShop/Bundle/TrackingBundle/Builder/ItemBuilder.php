<?php

namespace CoreShop\Bundle\TrackingBundle\Builder;

use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Model\DataObject\Fieldcollection;

class ItemBuilder implements ItemBuilderInterface
{
    /**
     * @var PurchasablePriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @param PurchasablePriceCalculatorInterface $productPriceCalculator
     */
    public function __construct(PurchasablePriceCalculatorInterface $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

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
        $item->setQuantity($quantity);
        $item->setPrice($this->productPriceCalculator->getPrice($product) / 100);

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
        $item->setPrice($this->productPriceCalculator->getPrice($product) / 100);

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
        $item->setRevenue($order->getTotal() / 100);
        $item->setShipping($order->getShipping() / 100);
        $item->setTax($order->getTotalTax() / 100);

        if ($order->getPriceRuleItems() instanceof Fieldcollection) {
            if ($order->getPriceRuleItems()->getCount() > 0) {
                foreach ($order->getPriceRuleItems() as $priceRule) {
                    if ($priceRule instanceof ProposalCartPriceRuleItemInterface) {
                        if ($priceRule->getCartPriceRule() instanceof CartPriceRuleInterface) {
                            $item->setCoupon($priceRule->getCartPriceRule()->getName());
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
            if ($item instanceof OrderItemInterface) {
                $items[] = $this->buildCheckoutItem($order, $item);
            }
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
        $item->setPrice($orderItem->getItemPrice() / 100);
        $item->setQuantity($orderItem->getQuantity());

        return $item;
    }
}
