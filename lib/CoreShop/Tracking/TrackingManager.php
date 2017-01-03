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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tracking;

use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\Product;

/**
 * Class TrackingManager
 * @package CoreShop\Tracking
 */
class TrackingManager
{
    /**
     * @var Tracker[]
     */
    public $tracker = [];

    /**
     * @var TrackingManager
     */
    protected static $instance;

    /**
     * TrackingManager constructor.
     */
    public function __construct()
    {
        if (\Pimcore::getDiContainer()->has("coreshop.tracker")) {
            $availableTracker = \Pimcore::getDiContainer()->get("coreshop.tracker");

            foreach ($availableTracker as $tracker) {
                if ($tracker instanceof Tracker) {
                    $tracker->init();

                    $this->tracker[] = $tracker;
                }
            }
        }
    }

    /**
     * @return TrackingManager
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $name
     * @param $params
     */
    protected function callMethod($name, $params)
    {
        foreach ($this->tracker as $tracker) {
            if (method_exists($tracker, $name)) {
                call_user_func_array([$tracker, $name], $params);
            }
        }
    }

    /**
     * @param Product $product
     * @return mixed
     */
    public function trackProductView(Product $product)
    {
        $this->callMethod("trackProductView", [$product]);
    }

    /**
     * @param Product $product
     * @return mixed
     */
    public function trackProductImpression(Product $product)
    {
        $this->callMethod("trackProductImpression", [$product]);
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public function trackProductActionAdd(Product $product, $quantity = 1)
    {
        $this->callMethod("trackProductActionAdd", [$product, $quantity]);
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public function trackProductActionRemove(Product $product, $quantity = 1)
    {
        $this->callMethod("trackProductActionRemove", [$product, $quantity]);
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckout(Cart $cart, $stepNumber = null, $checkoutOption = null)
    {
        $this->callMethod("trackCheckout", [$cart, $stepNumber, $checkoutOption]);
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckoutStep(Cart $cart, $stepNumber = null, $checkoutOption = null)
    {
        $this->callMethod("trackCheckoutStep", [$cart, $stepNumber, $checkoutOption]);
    }

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public function trackCheckoutAction(Cart $cart, $stepNumber = null, $checkoutOption = null)
    {
        $this->callMethod("trackCheckoutAction", [$cart, $stepNumber, $checkoutOption]);
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function trackCheckoutComplete(Order $order)
    {
        $this->callMethod("trackCheckoutComplete", [$order]);
    }
}
