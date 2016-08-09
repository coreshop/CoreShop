<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tracking\Google;

use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\PriceRule\AbstractPriceRule;
use CoreShop\Model\Product;
use CoreShop\Tracking\ActionData;
use CoreShop\Tracking\ImpressionData;
use CoreShop\Tracking\ProductData;

/**
 * Class ItemBuilder
 * @package CoreShop\Tracking\Google
 */
class ItemBuilder extends \CoreShop\Tracking\ItemBuilder {
    
    /**
     * Build a product view object
     *
     * @param Product $product
     * @return ProductData
     */
    public function buildProductViewItem(Product $product) {
        return $this->buildProductActionItem($product);
    }

    /**
     * Build a product action item object
     *
     * @param Product $product
     * @param int $quantity
     * @return ProductData
     */
    public function buildProductActionItem(Product $product, $quantity = 1) {
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
     * @param Product $product
     * @return ImpressionData
     */
    public function buildProductImpressionItem(Product $product) {
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
     * @param Order $order
     * @return ActionData
     */
    public function buildOrderAction(Order $order) {
        $item = new ActionData();
        $item->setId($order->getOrderNumber());
        $item->setRevenue($order->getTotal());
        $item->setShipping($order->getShipping());
        $item->setTax($order->getTotalTax());
        $item->setAffiliation($order->getShop()->getName());

        if($order->getPriceRule() instanceof AbstractPriceRule) {
            $item->setCoupon($order->getPriceRule()->getName());
        }

        return $item;
    }

    /**
     * Build checkout items
     *
     * @param Order $order
     * @return ProductData[]
     */
    public function buildCheckoutItems(Order $order) {
        $items = [];

        foreach($order->getItems() as $item) {
            $items[] = $this->buildCheckoutItem($order, $item);
        }

        return $items;
    }

    /**
     * Build checkout items by cart
     *
     * @param Cart $cart
     * @return mixed
     */
    public function buildCheckoutItemsByCart(Cart $cart) {
        $items = [];

        foreach ($cart->getItems() as $item) {
            $items[] = $this->buildProductActionItem($item->getProduct(), $item->getAmount());
        }

        return $items;
    }

    /**
     * Build a checkout item object
     *
     * @param Order $order
     * @param Order\Item $orderItem
     * @return ProductData
     */
    public function buildCheckoutItem(Order $order, Order\Item $orderItem) {
        $item = new ProductData();
        $item->setId($orderItem->getId());
        $item->setName($orderItem->getProduct()->getName());
        $item->setCategory($orderItem->getProduct()->getCategories()[0]->getName());
        $item->setPrice($orderItem->getPrice());
        $item->setQuantity($orderItem->getAmount());

        return $item;
    }
}