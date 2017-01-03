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

namespace CoreShop\Model\Product;

use CoreShop\Model\PriceRule\AbstractPriceRule;
use CoreShop\Model\PriceRule\Action\AbstractAction;
use CoreShop\Model\PriceRule\Condition\AbstractCondition;
use CoreShop\Model\Product;

/**
 * Class AbstractProductPriceRule
 * @package CoreShop\Model\Product
 */
abstract class AbstractProductPriceRule extends AbstractPriceRule
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = [];

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = [];

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

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
     * Get Specific Price for product.
     *
     * @param Product $product
     *
     * @return float|boolean
     */
    public function getPrice($product)
    {
        $price = false;
        $actions = $this->getActions();

        foreach ($actions as $action) {
            if ($action instanceof AbstractAction) {
                $actionsPrice = $action->getPrice($product);

                if ($actionsPrice !== false) {
                    $price = $actionsPrice;
                }
            }
        }

        return $price;
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
     * @return string
     */
    public function __toString()
    {
        return strval($this->getName());
    }
}
