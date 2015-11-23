<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\PriceRule\Action;

use Pimcore\Model;

class FreeShipping extends AbstractAction {
    /**
     * @var string
     */
    public $type = "freeShipping";

    /**
     * Calculate discount
     *
     * @param Model\Object\CoreShopCart $cart
     * @return int
     */
    public function getDiscount(Model\Object\CoreShopCart $cart) {
        $discount = $cart->getShipping();

        return $discount;
    }

    /**
     * Apply Rule to Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    public function applyRule(Model\Object\CoreShopCart $cart) {
        return true;
    }

    /**
     * Remove Rule from Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    public function unApplyRule(Model\Object\CoreShopCart $cart) {
        return true;
    }
}
