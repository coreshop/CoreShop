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

    public function getDiscount(Model\Object\CoreShopCart $cart)
    {
        $discount = $cart->getShipping();

        return $discount;
    }

    public function applyRule(Model\Object\CoreShopCart $cart)
    {
        return true;
    }
}
