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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\Country as CountryModel;


/**
 * Class Conditions
 * @package CoreShop\Model\PriceRule\Condition
 */
class Conditions extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'conditions';

    /**
     * @var AbstractCondition[]
     */
    public $conditions;

    /**
     * @var string
     */
    public $operator;

    /**
     * @return AbstractCondition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param AbstractCondition[] $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Cart       $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkConditionCart(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        return $this->check(function ($condition) use ($cart, $priceRule, $throwException) {
            return $condition->checkConditionCart($cart, $priceRule, $throwException);
        });
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param ProductModel $product
     * @param ProductModel\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
    {
        return $this->check(function ($condition) use ($product, $priceRule) {
            return $condition->checkConditionProduct($product, $priceRule);
        });
    }

    /**
     * @param $checkCondition
     * @return bool
     * @throws Exception
     */
    public function check($checkCondition)
    {
        $operator = $this->getOperator();

        foreach ($this->getConditions() as $condition) {
            $valid = $checkCondition($condition);

            if ($operator === "and") {
                if (!$valid) {
                    return false;
                }
            } elseif ($operator === "or") {
                if ($valid) {
                    return true;
                }
            }
        }

        return true;
    }
}
