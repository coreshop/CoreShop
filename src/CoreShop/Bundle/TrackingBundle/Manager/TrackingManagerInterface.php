<?php

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