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

namespace CoreShop\Bundle\TrackingBundle\Builder;

use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

interface ItemBuilderInterface
{
    /**
     * Build a product view object
     *
     * @param PurchasableInterface $product
     * @return ProductData
     */
    public function buildPurchasableViewItem(PurchasableInterface $product);

    /**
     * Build a product action item object
     *
     * @param PurchasableInterface $product
     * @param int $quantity
     * @return ProductData
     */
    public function buildPurchasableActionItem(PurchasableInterface $product, $quantity = 1);

    /**
     * Build a product impression object
     *
     * @param PurchasableInterface $product
     * @return ImpressionData
     */
    public function buildPurchasableImpressionItem(PurchasableInterface $product);

    /**
     * Build a checkout transaction object
     *
     * @param OrderInterface $order
     * @return ActionData
     */
    public function buildOrderAction(OrderInterface $order);

    /**
     * Build checkout items
     *
     * @param OrderInterface $order
     * @return ProductData[]
     */
    public function buildCheckoutItems(OrderInterface $order);

    /**
     * Build checkout items by cart
     *
     * @param CartInterface $cart
     * @return ProductData[]
     */
    public function buildCheckoutItemsByCart(CartInterface $cart);

    /**
     * @param CartInterface $cart
     * @return string
     */
    public function buildCouponByCart(CartInterface $cart);

    /**
     * Build a checkout item object
     *
     * @param OrderInterface $order
     * @param OrderItemInterface $orderItem
     * @return ProductData
     */
    public function buildCheckoutItem(OrderInterface $order, OrderItemInterface $orderItem);
}