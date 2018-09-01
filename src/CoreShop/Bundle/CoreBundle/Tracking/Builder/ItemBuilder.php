<?php

namespace CoreShop\Bundle\CoreBundle\Tracking\Builder;

use CoreShop\Bundle\TrackingBundle\Builder\ItemBuilderInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Product\Model\ProductInterface;

final class ItemBuilder implements ItemBuilderInterface
{
    /**
     * @var ItemBuilderInterface
     */
    protected $decoratedItemBuilder;

    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    protected $taxedProductPriceCalculator;


    /**
     * @param ItemBuilderInterface $decoratedItemBuilder
     * @param TaxedProductPriceCalculatorInterface $taxedProductPriceCalculator
     */
    public function __construct(
        ItemBuilderInterface $decoratedItemBuilder,
        TaxedProductPriceCalculatorInterface $taxedProductPriceCalculator
    )
    {
        $this->decoratedItemBuilder = $decoratedItemBuilder;
        $this->taxedProductPriceCalculator = $taxedProductPriceCalculator;
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

        if ($product instanceof \CoreShop\Component\Core\Model\ProductInterface) {
            $item->setPrice($this->taxedProductPriceCalculator->getPrice($product) / 100);
        }

        if (method_exists($product, 'getManufacturer')) {
            if ($product->getManufacturer() instanceof ManufacturerInterface) {
                $item->setBrand($product->getManufacturer()->getName());
            }
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     * @throws \ReflectionException
     */
    public function buildPurchasableImpressionItem(PurchasableInterface $product)
    {
        var_dump(get_class($product));
        $item = $this->decoratedItemBuilder->buildPurchasableImpressionItem($product);

        if ($product instanceof ProductInterface) {
            if (count($product->getCategories()) > 0) {
                $item->setCategory($product->getCategories()[0]->getName());
            }
        }

        if ($product instanceof \CoreShop\Component\Core\Model\ProductInterface) {
            $item->setPrice($this->taxedProductPriceCalculator->getPrice($product) / 100);
        }

        if (method_exists($product, 'getManufacturer')) {
            if ($product->getManufacturer() instanceof ManufacturerInterface) {
                $item->setBrand($product->getManufacturer()->getName());
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
        $items = [];
        foreach ($order->getItems() as $item) {
            if ($item instanceof OrderItemInterface) {
                $items[] = $this->buildCheckoutItem($order, $item);
            }
        }

        return $items;
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
    public function buildCouponByCart(CartInterface $cart)
    {
        return $this->decoratedItemBuilder->buildCouponByCart($cart);
    }

    /**
     * {@inheritdoc}
     * @throws \ReflectionException
     */
    public function buildCheckoutItem(OrderInterface $order, OrderItemInterface $orderItem)
    {
        $item = $this->decoratedItemBuilder->buildCheckoutItem($order, $orderItem);
        $product = $orderItem->getProduct();

        if (method_exists($product, 'getSku')) {
            $item->setSku($product->getSku());
        }

        if ($product instanceof ProductInterface) {
            if (count($product->getCategories()) > 0) {
                $item->setCategory($product->getCategories()[0]->getName());
            }
        }

        return $item;
    }

}
