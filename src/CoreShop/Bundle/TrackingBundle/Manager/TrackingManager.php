<?php

namespace CoreShop\Bundle\TrackingBundle\Manager;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

class TrackingManager implements TrackingManagerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $tracker;

    /**
     * @param ServiceRegistryInterface $tracker
     */
    public function __construct(ServiceRegistryInterface $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * @param $name
     * @param $params
     */
    protected function callMethod($name, $params)
    {
        foreach ($this->tracker->all() as $tracker) {
            if (method_exists($tracker, $name)) {
                call_user_func_array([$tracker, $name], $params);
            }
        }
    }

    /**
     * @param PurchasableInterface $product
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        $this->callMethod("trackPurchasableView", [$product]);
    }

    /**
     * @param PurchasableInterface $product
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        $this->callMethod("trackPurchasableImpression", [$product]);
    }

    /**
     * @param PurchasableInterface $product
     * @param int $quantity
     */
    public function trackPurchasableActionAdd(PurchasableInterface $product, $quantity = 1)
    {
        $this->callMethod("trackPurchasableActionAdd", [$product, $quantity]);
    }

    /**
     * @param PurchasableInterface $product
     * @param int $quantity
     */
    public function trackPurchasableActionRemove(PurchasableInterface $product, $quantity = 1)
    {
        $this->callMethod("trackPurchasableActionRemove", [$product, $quantity]);
    }

    /**
     * @param CartInterface $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     */
    public function trackCheckout(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        $this->callMethod("trackCheckout", [$cart, $stepNumber, $checkoutOption]);
    }

    /**
     * @param CartInterface $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     */
    public function trackCheckoutStep(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        $this->callMethod("trackCheckoutStep", [$cart, $stepNumber, $checkoutOption]);
    }

    /**
     * @param CartInterface $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     */
    public function trackCheckoutAction(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        $this->callMethod("trackCheckoutAction", [$cart, $stepNumber, $checkoutOption]);
    }

    /**
     * @param OrderInterface $order
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $this->callMethod("trackCheckoutComplete", [$order]);
    }
}