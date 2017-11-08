<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

interface TrackerInterface
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
     * @param PurchasableInterface $product
     * @param int $quantity
     */
    public function trackPurchasableActionAdd(PurchasableInterface $product, $quantity = 1);

    /**
     * @param PurchasableInterface $product
     * @param int $quantity
     */
    public function trackPurchasableActionRemove(PurchasableInterface $product, $quantity = 1);

    /**
     * @param CartInterface $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     */
    public function trackCheckout(CartInterface $cart, $stepNumber = null, $checkoutOption = null);

    /**
     * @param CartInterface $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     */
    public function trackCheckoutStep(CartInterface $cart, $stepNumber = null, $checkoutOption = null);

    /**
     * @param CartInterface $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     */
    public function trackCheckoutAction(CartInterface $cart, $stepNumber = null, $checkoutOption = null);

    /**
     * @param OrderInterface $order
     */
    public function trackCheckoutComplete(OrderInterface $order);
}