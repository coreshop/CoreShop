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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Cart\PriceRule;

abstract class AbstractCondition extends Model\PriceRule\AbstractActionCondition
{
    /**
     * @var string
     */
    public $elementType = '';

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Cart $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return mixed
     */
    abstract public function checkConditionCart(Model\Cart $cart, PriceRule $priceRule, $throwException = false);

    /**
     * Check if Product is Valid for Condition.
     *
     * @param Model\Product $product
     * @param Model\Product\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    abstract public function checkConditionProduct(Model\Product $product, Model\Product\AbstractProductPriceRule $priceRule);
}
