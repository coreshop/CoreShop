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
 * Class Tracker
 * @package CoreShop\Tracking
 */
abstract class Tracker {

    /**
     * Tracker constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return \Zend_View
     */
    protected function buildView()
    {
        $view = new \Zend_View();

        $class = get_class($this);
        $class = explode('\\', $class);
        $class = array_pop($class);
        $class = strtolower(preg_replace('/(?<=[a-z])([A-Z]+)/', "-$1", $class));
        $class = strtolower($class);

        $view->setScriptPath(
            array(
                CORESHOP_PATH . '/views/scripts/tracking/' . $class,
                CORESHOP_TEMPLATE_BASE . '/scripts/tracking/' . $class,
                CORESHOP_TEMPLATE_PATH . '/scripts/tracking/' . $class,
                PIMCORE_WEBSITE_PATH.'/views/scripts/coreshop/tracking/' . $class,
            )
        );

        return $view;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    public abstract function track($config);

    /**
     */
    public abstract function init();

    /**
     * @param Product $product
     * @return mixed
     */
    public abstract function trackProductView(Product $product);

    /**
     * @param Product $product
     * @return mixed
     */
    public abstract function trackProductImpression(Product $product);

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public abstract function trackProductActionAdd(Product $product, $quantity = 1);

    /**
     * @param Product $product
     * @param int $quantity
     * @return mixed
     */
    public abstract function trackProductActionRemove(Product $product, $quantity = 1);

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public abstract function trackCheckout(Cart $cart, $stepNumber = null, $checkoutOption = null);

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public abstract function trackCheckoutStep(Cart $cart, $stepNumber = null, $checkoutOption = null);

    /**
     * @param Cart $cart
     * @param null $stepNumber
     * @param null $checkoutOption
     * @return mixed
     */
    public abstract function trackCheckoutAction(Cart $cart, $stepNumber = null, $checkoutOption = null);

    /**
     * @param Order $order
     * @return mixed
     */
    public abstract function trackCheckoutComplete(Order $order);

    /**
     * @return ItemBuilder
     */
    public abstract function getItemBuilder();

    /**
     * Remove null values from an object, keep protected keys in any case
     *
     * @param $data
     * @param array $protectedKeys
     * @return array
     */
    protected function filterNullValues($data, $protectedKeys = [])
    {
        $result = [];
        foreach ($data as $key => $value) {
            $isProtected = in_array($key, $protectedKeys);
            if (null !== $value || $isProtected) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}