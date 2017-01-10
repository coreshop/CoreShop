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

namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule as CarrierShippingRule;

/**
 * Class Conditions
 * @package CoreShop\Model\Carrier\ShippingRule\Condition
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
     * @param Model\Carrier $carrier
     * @param Model\Cart $cart
     * @param Model\User\Address $address
     * @param CarrierShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Model\Carrier $carrier, Model\Cart $cart, Model\User\Address $address, CarrierShippingRule $shippingRule)
    {
        return $this->check(function (AbstractCondition $condition) use ($carrier, $cart, $address, $shippingRule) {
            return $condition->checkCondition($carrier, $cart, $address, $shippingRule);
        });
    }

    /**
     * @param \Closure $checkCondition
     * @return bool
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
