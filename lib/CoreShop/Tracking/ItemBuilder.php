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

namespace CoreShop\Tracking;
use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\Product;

/**
 * Class TrackingItemBuilder
 * @package CoreShop\Tracking
 */
abstract class ItemBuilder
{
    /**
     * Build a product view object
     *
     * @param Product $product
     * @return ProductData
     */
    public abstract function buildProductViewItem(Product $product);

    /**
     * Build a product action item object
     *
     * @param Product $product
     * @return ProductData
     */
    public abstract function buildProductActionItem(Product $product);

    /**
     * Build a product impression object
     *
     * @param Product $product
     * @return ImpressionData
     */
    public abstract function buildProductImpressionItem(Product $product);

    /**
     * Build a checkout transaction object
     *
     * @param Order $order
     * @return ActionData
     */
    public abstract function buildOrderAction(Order $order);

    /**
     * Build checkout items
     *
     * @param Order $order
     * @return ProductData[]
     */
    public abstract function buildCheckoutItems(Order $order);

    /**
     * Build checkout items by cart
     *
     * @param Cart $cart
     * @return mixed
     */
    public abstract function buildCheckoutItemsByCart(Cart $cart);

    /**
     * Build a checkout item object
     *
     * @param Order $order
     * @param Order\Item $orderItem
     * @return ProductData
     */
    public abstract function buildCheckoutItem(Order $order, Order\Item $orderItem);
}