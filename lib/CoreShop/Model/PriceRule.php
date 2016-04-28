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

namespace CoreShop\Model;

use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopCart;

class PriceRule extends AbstractModel
{
    /**
     * possible types of a condition
     * @var array
     */
    public static $availableConditions = array("customer", "timeSpan", "amount", "totalAvailable", "totalPerCustomer", "country", "product", "category", "customerGroup", "zone");

    /**
     * possible types of a action
     * @var array
     */
    public static $availableActions = array("freeShipping", "discountAmount", "discountPercent", "gift");

    /**
     * Add Condition Type
     *
     * @param $condition
     */
    public static function addCondition($condition)
    {
        if (!in_array($condition, self::$availableConditions)) {
            self::$availableConditions[] = $condition;
        }
    }

    /**
     * Add Action Type
     *
     * @param $action
     */
    public static function addAction($action)
    {
        if (!in_array($action, self::$availableActions)) {
            self::$availableActions[] = $action;
        }
    }

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $description;

    /**
     * @var boolean
     */
    public $active;

    /**
     * @var boolean
     */
    public $highlight;

    /**
     * @var array
     */
    public $conditions;

    /**
     * @var array
     */
    public $actions;

    /**
     * Get PriceRule by ID
     *
     * @param $id
     * @return PriceRule|null
     */
    public static function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * Get PriceRule by Code
     *
     * @param $code
     * @return PriceRule|null
     */
    public static function getByCode($code)
    {
        return parent::getByField("code", $code);
    }

    /**
     * Get al PriceRules
     *
     * @return array
     */
    public static function getPricingRules()
    {
        $list = new PriceRule\Listing();

        return $list->getData();
    }

    /**
     * Get public PriceRules
     *
     * @return array
     */
    public static function getHighlightItems()
    {
        $cart = Tool::prepareCart();

        $priceRules = new PriceRule\Listing();
        $priceRules->setCondition("(code IS NOT NULL AND code <> '') AND highlight = 1");

        $priceRules = $priceRules->getData();

        $availablePriceRules = array();

        foreach ($priceRules as $priceRule) {
            if ($priceRule->checkValidity($cart, false, true)) {
                if ($cart->getPriceRule() instanceof PriceRule && $priceRule->getId() == $cart->getPriceRule()->getId()) {
                    continue;
                }

                $availablePriceRules[] = $priceRule;
            }
        }

        return $availablePriceRules;
    }

    /**
     * Remove default PriceRule from Cart
     *
     * @param CoreShopCart|null $cart
     */
    public static function autoRemoveFromCart(CoreShopCart $cart = null)
    {
        if ($cart == null) {
            $cart = Tool::prepareCart();
        }

        if ($cart->getPriceRule() instanceof PriceRule) {
            if (!$cart->getPriceRule()->checkValidity($cart, false, true)) {
                $cart->removePriceRule();
            }
        }
    }

    /**
     * Add default PriceRule to Cart
     *
     * @param Cart|null $cart
     * @return bool
     */
    public static function autoAddToCart(Cart $cart = null)
    {
        if ($cart == null) {
            $cart = Tool::prepareCart();
        }

        if ($cart->getPriceRule() == null) {
            $priceRules = new PriceRule\Listing();
            $priceRules->setCondition("code IS NULL OR code = ''");

            $priceRules = $priceRules->getData();

            foreach ($priceRules as $priceRule) {
                if ($priceRule instanceof PriceRule) {
                    if ($priceRule->checkValidity($cart, false)) {
                        $cart->addPriceRule($priceRule);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Check if PriceRule is Valid for Cart
     *
     * @param Cart $cart
     * @param bool|false $throwException
     * @param bool|false $alreadyInCart
     * @return bool
     */
    public function checkValidity(Cart $cart = null, $throwException = false, $alreadyInCart = false)
    {
        if (is_null($cart)) {
            $cart = Tool::prepareCart();
        }

        //Price Rule without actions doesnt make any sense
        if (count($this->getActions()) <= 0) {
            return false;
        }

        if ($this->getConditions()) {
            foreach ($this->getConditions() as $condition) {
                if ($condition instanceof PriceRule\Condition\AbstractCondition) {
                    if (!$condition->checkCondition($cart, $this, $throwException)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Applies Rules to Cart
     * 
     * @param Cart $cart
     */
    public function applyRules(Cart $cart)
    {
        if(is_null($cart))
            $cart = Tool::prepareCart();

        foreach ($this->getActions() as $action) {
            if ($action instanceof PriceRule\Action\AbstractAction) {
                $action->applyRule($cart);
            }
        }
    }

    /**
     * Removes Rules from Cart
     */
    public function unApplyRules()
    {
        $cart = Tool::prepareCart();

        foreach ($this->getActions() as $action) {
            if ($action instanceof PriceRule\Action\AbstractAction) {
                $action->unApplyRule($cart);
            }
        }
    }

    /**
     * Get Discount for PriceRule
     *
     * @return int
     */
    public function getDiscount()
    {
        $cart = Tool::prepareCart();
        $discount = 0;

        if ($this->getActions()) {
            foreach ($this->getActions() as $action) {
                if ($action instanceof PriceRule\Action\AbstractAction) {
                    $discount += $action->getDiscount($cart);
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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return boolean
     */
    public function getHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param boolean $highlight
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->getName());
    }
}
