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

namespace CoreShop\Bundle\TrackingBundle\Manager;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

interface TrackingManagerInterface
{

    /**
     * @param PurchasableInterface $product
     */
    public function trackPurchasableView(PurchasableInterface $product);

    /**
     * @param PurchasableInterface $product
     */
    public function trackPurchasableImpression(PurchasableInterface $product);

    /**
     * @param CartInterface        $cart
     * @param PurchasableInterface $product
     * @param int                  $quantity
     */
    public function trackCartPurchasableActionAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1);

    /**
     * @param CartInterface        $cart
     * @param PurchasableInterface $product
     * @param int                  $quantity
     */
    public function trackCartPurchasableActionRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1);

    /**
     * @param CartInterface $cart
     * @param null          $stepIdentifier
     * @param null          $checkoutOption
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $checkoutOption = null);

    /**
     * @param CartInterface $cart
     * @param null          $stepNumber
     * @param null          $checkoutOption
     */
    public function trackCheckoutAction(CartInterface $cart, $stepNumber = null, $checkoutOption = null);

    /**
     * @param OrderInterface $order
     */
    public function trackCheckoutComplete(OrderInterface $order);
}