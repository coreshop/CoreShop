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

use CoreShop\Bundle\TrackingBundle\Tracker\EcommerceTrackerInterface;
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
            if (!$tracker instanceof EcommerceTrackerInterface) {
                continue;
            }

            if (!$tracker->isEnabled()) {
                continue;
            }

            if (method_exists($tracker, $name)) {
                call_user_func_array([$tracker, $name], $params);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableView(PurchasableInterface $product)
    {
        $this->callMethod('trackPurchasableView', [$product]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackPurchasableImpression(PurchasableInterface $product)
    {
        $this->callMethod('trackPurchasableImpression', [$product]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableAdd(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        $this->callMethod('trackCartPurchasableAdd', [$cart, $product, $quantity]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCartPurchasableRemove(CartInterface $cart, PurchasableInterface $product, $quantity = 1)
    {
        $this->callMethod('trackCartPurchasableRemove', [$cart, $product, $quantity]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutStep(CartInterface $cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null)
    {
        $this->callMethod('trackCheckoutStep', [$cart, $stepIdentifier, $isFirstStep, $checkoutOption]);
    }

    /**
     * {@inheritdoc}
     */
    public function trackCheckoutComplete(OrderInterface $order)
    {
        $this->callMethod('trackCheckoutComplete', [$order]);
    }
}
