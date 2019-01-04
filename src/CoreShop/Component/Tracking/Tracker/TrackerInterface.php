<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Tracking\Tracker;

interface TrackerInterface
{
    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @param mixed $product
     */
    public function trackProduct($product);

    /**
     * @param mixed $product
     */
    public function trackProductImpression($product);

    /**
     * @param mixed $cart
     * @param mixed $product
     * @param int   $quantity
     */
    public function trackCartAdd($cart, $product, $quantity = 1);

    /**
     * @param mixed $cart
     * @param mixed $product
     * @param int   $quantity
     */
    public function trackCartRemove($cart, $product, $quantity = 1);

    /**
     * @param mixed $cart
     * @param null  $stepIdentifier
     * @param bool  $isFirstStep
     * @param null  $checkoutOption
     */
    public function trackCheckoutStep($cart, $stepIdentifier = null, $isFirstStep = false, $checkoutOption = null);

    /**
     * @param mixed $order
     */
    public function trackCheckoutComplete($order);
}
