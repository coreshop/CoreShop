<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\SpecificPrice\Condition;

use CoreShop\Model\PriceRule\AbstractPriceRule;
use CoreShop\Model;
use CoreShop\Model\PriceRule;

abstract class AbstractCondition extends Model\Product\SpecificPrice\AbstractSpecificPrice {
    /**
     * @var string
     */
    public $elementType = "condition";

    /**
     * Check if Product is Valid for Condition
     *
     * @param Model\Product $product
     * @param Model\Product\SpecificPrice $specificPrice
     * @return boolean
     */
    public abstract function checkCondition(Model\Product $product, Model\Product\SpecificPrice $specificPrice);
}
