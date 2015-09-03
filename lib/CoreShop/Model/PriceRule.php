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

namespace CoreShop\Model;

use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopCart;

class PriceRule extends AbstractModel {

    /**
     * possible types of a condition
     * @var array
     */
    static $availableConditions = array("customer", "timeSpan", "amount", "totalAvailable", "totalPerCustomer", "country", "product", "category");

    /**
     * possible types of a action
     * @var array
     */
    static $availableActions = array("freeShipping", "discountAmount", "discountPercent", "gift");

    /**
     * @param $condition
     */
    public static function addCondition($condition) {
        if(!in_array($condition, self::$availableConditions)) {
            self::$availableConditions[] = $condition;
        }
    }

    /**
     * @param $action
     */
    public static function addAction($action) {
        if(!in_array($action, self::$availableActions)) {
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

    public function save() {
        return $this->getResource()->save();
    }

    public static function getById($id) {
        try {
            $obj = new self;
            $obj->getResource()->getById($id);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    public static function getByCode($code) {
        try {
            $obj = new self;
            $obj->getResource()->getByCode($code);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    public static function getPricingRules()
    {
        $list = new PriceRule\Listing();

        return $list->getData();
    }

    public static function getHighlightItems()
    {
        $cart = Tool::prepareCart();

        $cartRules = new PriceRule\Listing();
        $cartRules->setCondition("(code IS NOT NULL AND code <> '') AND highlight = 1");

        $cartRules = $cartRules->getData();

        $availableCartRules = array();

        foreach($cartRules as $cartRule)
        {
            if($cartRule->checkValidity(false, true))
            {
                if($cart->getPriceRule() instanceof PriceRule && $cartRule->getId() == $cart->getPriceRule()->getId()) {
                    continue;
                }

                $availableCartRules[] = $cartRule;
            }
        }

        return $availableCartRules;
    }


    public static function autoRemoveFromCart(CoreShopCart $cart = null)
    {
        if($cart == null)
            $cart = Tool::prepareCart();

        if($cart->getPriceRule() instanceof PriceRule) {
            if (!$cart->getPriceRule()->checkValidity(false, true)) {
                die("invalid");
                $cart->removeCartRule();
            }
        }
    }

    public static function autoAddToCart(CoreShopCart $cart = null)
    {
        if($cart == null)
            $cart = Tool::prepareCart();

        if($cart->getPriceRule() == null) {
            $cartRules = new PriceRule\Listing();
            $cartRules->setCondition("code IS NULL OR code = ''");
            //$cartRules->setOrderKey("priority");
            //$cartRules->setOrder("DESC");

            $cartRules = $cartRules->getData();

            foreach ($cartRules as $cartRule) {
                if ($cartRule->checkValidity(false)) {
                    $cart->addCartRule($cartRule);
                }
            }

            return true;
        }

        return false;
    }

    public function checkValidity($throwException = false, $alreadyInCart = false)
    {
        $cart = Tool::prepareCart();

        foreach($this->getConditions() as $condition) {
            if(!$condition->checkCondition($cart, $this, $throwException)) {
                return false;
            }
        }

        return true;
    }

    public function applyRules() {
        $cart = Tool::prepareCart();

        foreach($this->getActions() as $action) {
            $action->applyRule($cart);
        }
    }

    public function unApplyRules() {
        $cart = Tool::prepareCart();

        foreach($this->getActions() as $action) {
            $action->unApplyRule($cart);
        }
    }

    public function getDiscount()
    {
        $cart = Tool::prepareCart();
        $discount = 0;

        foreach($this->getActions() as $action) {
            $discount += $action->getDiscount($cart);
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

    public function __toString() {
        return strval($this->getName());
    }
}