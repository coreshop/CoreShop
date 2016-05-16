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
namespace CoreShop\Model\Cart\PriceRule\Condition;

use CoreShop\Model\Cart\PriceRule\AbstractPriceRule;
use CoreShop\Model;
use CoreShop\Model\Cart\PriceRule;

abstract class AbstractCondition extends AbstractPriceRule
{
    /**
     * @var string
     */
    public $elementType = 'condition';

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Cart $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return mixed
     */
    abstract public function checkCondition(Model\Cart $cart, PriceRule $priceRule, $throwException = false);
}
