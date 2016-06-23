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

namespace CoreShop\Model\Product;

use CoreShop\Model\PriceRule\AbstractPriceRule;
use CoreShop\Model\PriceRule\Action\AbstractAction;
use CoreShop\Model\PriceRule\Condition\AbstractCondition;
use CoreShop\Model\Product;

abstract class AbstractProductPriceRule extends AbstractPriceRule
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array();

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = array();

    /**
     * @var string
     */
    public static $type;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $conditions;

    /**
     * @var array
     */
    public $actions;

    /**
     * Add Condition Type.
     *
     * @param $condition
     */
    public static function addCondition($condition)
    {
        if (!in_array($condition, self::$availableConditions)) {
            $class = get_called_class();
            $class::$availableConditions[] = $condition;
        }
    }

    /**
     * Add Action Type.
     *
     * @param $action
     */
    public static function addAction($action)
    {
        if (!in_array($action, self::$availableActions)) {
            $class = get_called_class();
            $class::$availableActions[] = $action;
        }
    }

    /**
     * Check if PriceRule is Valid for Cart.
     *
     * @param Product $product
     *
     * @return bool
     */
    public function checkValidity(Product $product = null)
    {
        if (is_null($product)) {
            return false;
        }

        //Price Rule without actions doesnt make any sense
        if (count($this->getActions()) <= 0) {
            return false;
        }

        if ($this->getConditions()) {
            foreach ($this->getConditions() as $condition) {
                if ($condition instanceof AbstractCondition) {
                    if (!$condition->checkConditionProduct($product, $this)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Get Discount for PriceRule.
     *
     * @param float $basePrice
     * @param Product $product
     *
     * @return float
     */
    public function getDiscount($basePrice, Product $product)
    {
        $discount = 0;

        if ($this->getActions()) {
            foreach ($this->getActions() as $action) {
                if ($action instanceof AbstractAction) {
                    $discount += $action->getDiscountProduct($basePrice, $product);
                }
            }
        }

        return $discount;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getConditions()
    {
        if (!is_array($this->conditions)) {
            $this->conditions = array();
        }

        return $this->conditions;
    }

    /**
     * @param array $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        if (!is_array($this->actions)) {
            $this->actions = array();
        }

        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        $class = get_called_class();

        return $class::$type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->getName());
    }
}
