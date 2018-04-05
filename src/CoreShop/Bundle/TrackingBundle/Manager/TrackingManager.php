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
        $this->callMethod('trackPurchasableView', [$product]);
    }

    /**
     * @param PurchasableInterface $product
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        $this->callMethod('trackPurchasableImpression', [$product]);
    }

    /**
     * @param CartInterface $cart
     * @param PurchasableInterface $product
     * @param int $quantity
     */
    public function trackCartPurchasableActionAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        $this->callMethod('trackCartPurchasableActionAdd', [$cart, $product, $quantity]);
    }

    /**
     * @param CartInterface $cart
     * @param PurchasableInterface $product
     * @param int $quantity
     */
    public function trackCartPurchasableActionRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        $this->callMethod('trackCartPurchasableActionRemove', [$cart, $product, $quantity]);
    }

    /**
     * @param CartInterface $cart
     * @param null $stepIdentifier
     * @param null $checkoutOption
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $checkoutOption = null)
    {
        $this->callMethod('trackCheckoutStep', [$cart, $stepIdentifier, $checkoutOption]);
    }

    /**
     * @param CartInterface $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     */
    public function trackCheckoutAction(CartInterface $cart, $stepNumber = null, $checkoutOption = null)
    {
        $this->callMethod('trackCheckoutAction', [$cart, $stepNumber, $checkoutOption]);
    }

    /**
     * @param OrderInterface $order
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $this->callMethod('trackCheckoutComplete', [$order]);
    }
}