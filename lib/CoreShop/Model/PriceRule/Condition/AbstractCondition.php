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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Model\PriceRule\AbstractPriceRule;
use CoreShop\Model;
use CoreShop\Model\PriceRule;

abstract class AbstractCondition extends AbstractPriceRule {
    /**
     * @var string
     */
    public $elementType = "condition";

    /**
     * Check if Cart is Valid for Condition
     *
     * @param Model\Cart $cart
     * @param PriceRule $priceRule
     * @param bool|false $throwException
     * @return mixed
     */
    public abstract function checkCondition(Model\Cart $cart, PriceRule $priceRule, $throwException = false);
}
