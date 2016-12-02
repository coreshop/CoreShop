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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Cart;

use CoreShop\Exception;
use CoreShop\Model\AbstractModel;
use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\PriceRule\AbstractPriceRule;
use CoreShop\Model\PriceRule\Action\AbstractAction;
use CoreShop\Model\PriceRule\Condition\AbstractCondition;
use CoreShop\Model\PriceRule\Item as PriceRuleItem;
use Pimcore\Db;

/**
 * Class PriceRule
 * @package CoreShop\Model\Cart
 */
class PriceRule extends AbstractPriceRule
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array('conditions', 'customers', 'timeSpan', 'amount', 'totalPerCustomer', 'countries', 'products', 'categories', 'customerGroups', 'zones', 'personas', 'carriers', 'currencies');

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = array('freeShipping', 'discountAmount', 'discountPercent', 'gift');

    /**
     * @var string
     */
    public static $type = "cartRule";

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
     * @var bool
     */
    public $active;

    /**
     * @var bool
     */
    public $highlight;

    /**
     * @var int
     */
    public $usagePerVoucherCode;

    /**
     * @var boolean
     */
    public $useMultipleVoucherCodes;

    /**
     * Get PriceRule by Code.
     *
     * @param $code
     *
     * @return PriceRule|null
     */
    public static function getByCode($code)
    {
        //Search for a PriceRule with a single code
        $priceRule = parent::getByField('code', $code);

        if ($priceRule instanceof PriceRule) {
            return $priceRule;
        }

        //Search for a PriceRule with multiple codes
        $voucherList = PriceRule\VoucherCode::getList();
        $voucherList->setCondition("code = ?", $code);
        $voucherData = $voucherList->getData();

        if (count($voucherData) > 0) {
            return $voucherData[0]->getPriceRule();
        }

        return null;
    }

    /**
     * Get al PriceRules.
     *
     * @return array
     */
    public static function getPricingRules()
    {
        $list = PriceRule::getList();

        return $list->getData();
    }

    /**
     * Get public PriceRules.
     *
     * @param Cart $cart
     *
     * @return array
     */
    public static function getHighlightItems(Cart $cart)
    {
        $priceRules = PriceRule::getList();
        $priceRules->setCondition("(code IS NOT NULL AND code <> '') AND highlight = 1");

        $priceRules = $priceRules->getData();

        $availablePriceRules = array();

        foreach ($priceRules as $priceRule) {
            if ($priceRule instanceof PriceRule) {
                if ($priceRule->checkValidity($cart, false, true)) {
                    $found = false;

                    foreach ($cart->getPriceRuleFieldCollection()->getItems() as $rule) {
                        if ($rule instanceof PriceRule) {
                            if ($rule->getId() === $priceRule->getId()) {
                                $found = true;
                                break;
                            }
                        }
                    }

                    if ($found) {
                        continue;
                    }

                    $availablePriceRules[] = $priceRule;
                }
            }
        }

        return $availablePriceRules;
    }

    /**
     * Remove default PriceRule from Cart.
     *
     * @param Cart|null $cart
     */
    public static function autoRemoveFromCart(Cart $cart)
    {
        foreach ($cart->getPriceRules() as $priceRuleItem) {
            $priceRule = $priceRuleItem->getPriceRule();

            if ($priceRule instanceof PriceRule) {
                if (!$priceRule->checkValidity($cart, null, false, true)) {
                    $cart->removePriceRule($priceRule);
                }
            }
        }
    }

    /**
     * Add default PriceRule to Cart.
     *
     * @param Cart|null $cart
     *
     * @return bool
     */
    public static function autoAddToCart(Cart $cart)
    {
        if (count($cart->getItems()) <= 0) {
            return false;
        }

        $priceRules = PriceRule::getList();
        $priceRules->setCondition("(code IS NULL OR code = '') AND useMultipleVoucherCodes = 0");

        $priceRules = $priceRules->getData();

        foreach ($priceRules as $priceRule) {
            if ($priceRule instanceof self) {
                if ($priceRule->checkValidity($cart, null, false)) {
                    $cart->addPriceRule($priceRule, $priceRule->getCode());
                }
            }
        }

        return true;
    }

    /**
     * @param $priceRule
     * @param $voucherCode
     *
     * @return int
     */
    public static function getTotalUsesForPriceRule(PriceRule $priceRule, $voucherCode = null)
    {
        $orderId = Order::classId();
        $priceRuleCollection = PriceRuleItem::getFieldCollectionType();

        echo $priceRuleCollection;

        $table = "object_collection_" . $priceRuleCollection . "_" . $orderId;

        $sql = "SELECT o_id as cnt FROM " . $table . " WHERE fieldname='priceRuleFieldCollection' AND priceRule=? AND " .
            ($voucherCode === null ? "voucherCode is NULL" : "voucherCode = ?") . " GROUP BY o_id";
        $sqlWrapper = "SELECT count(*) as cnt FROM ($sql) as query";

        $params = [$priceRule->getId()];

        if(!is_null($voucherCode)) {
            $params[] = $voucherCode;
        }

        $db = Db::get();
        $result = $db->fetchRow($sqlWrapper, $params);

        return $result['cnt'];
    }

    /**
     * Check if PriceRule is Valid for Cart.
     *
     * @param Cart       $cart
     * @param string     $voucherCode
     * @param bool|false $throwException
     * @param bool|false $alreadyInCart
     *
     * @throws Exception
     *
     * @return bool
     */
    public function checkValidity(Cart $cart, $voucherCode = null, $throwException = false, $alreadyInCart = false)
    {
        if (!$this->getActive()) {
            if($throwException) {
                throw new Exception("PriceRule is inactive");
            }
            else {
                return false;
            }
        }

        //Carts without any items are invalid
        if (count($cart->getItems()) <= 0) {
            return false;
        }

        //Price Rule without actions do not make sense
        if (count($this->getActions()) <= 0) {
            return false;
        }

        if ($this->getUsagePerVoucherCode() > 0 && $voucherCode) {
            $totalUses = self::getTotalUsesForPriceRule($this, $this->getCode() === $voucherCode ? null : $voucherCode);

            if ($totalUses >= intval($this->getUsagePerVoucherCode())) {
                if ($throwException) {
                    throw new Exception('You cannot use this voucher anymore (usage limit reached)');
                } else {
                    return false;
                }
            }
        }

        if ($this->getConditions()) {
            foreach ($this->getConditions() as $condition) {
                if ($condition instanceof AbstractCondition) {
                    if (!$condition->checkConditionCart($cart, $this, $throwException)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Applies Rules to Cart.
     * 
     * @param Cart $cart
     */
    public function applyRules(Cart $cart)
    {
        foreach ($this->getActions() as $action) {
            if ($action instanceof AbstractAction) {
                $action->applyRule($cart);
            }
        }
    }

    /**
     * Removes Rules from Cart.
     *
     * @param Cart $cart
     */
    public function unApplyRules(Cart $cart)
    {
        foreach ($this->getActions() as $action) {
            if ($action instanceof AbstractAction) {
                $action->unApplyRule($cart);
            }
        }
    }

    /**
     * apply price rule on order
     *
     * @param Order $order
     * @param PriceRuleItem $item
     */
    public function applyOrder(Order $order, PriceRuleItem $item)
    {
        if ($item->getVoucherCode()) {
            $voucherCode = Cart\PriceRule\VoucherCode::getByCode($item->getVoucherCode());

            if ($voucherCode instanceof Cart\PriceRule\VoucherCode) {
                $voucherCode->increaseUsage();
            }
        }
    }

    /**
     * un apply price rule on order
     *
     * @param Order $order
     * @param PriceRuleItem $item
     */
    public function unApplyOrder(Order $order, PriceRuleItem $item)
    {
        if ($item->getVoucherCode()) {
            $voucherCode = Cart\PriceRule\VoucherCode::getByCode($item->getVoucherCode());

            if ($voucherCode instanceof Cart\PriceRule\VoucherCode) {
                $voucherCode->decreaseUsage();
            }
        }
    }

    /**
     * Get Discount for PriceRule.
     *
     * @param Cart $cart
     * @param boolean $withTax
     *
     * @return int
     */
    public function getDiscount(Cart $cart, $withTax = true)
    {
        $discount = 0;

        if ($this->getActions()) {
            foreach ($this->getActions() as $action) {
                if ($action instanceof AbstractAction) {
                    $discount += $action->getDiscountCart($cart, $withTax);
                }
            }
        }

        return $discount;
    }

    /**
     * @return Cart\PriceRule\VoucherCode[]
     */
    public function getVoucherCodes()
    {
        $list = Cart\PriceRule\VoucherCode::getList();
        $list->setCondition("priceRuleId = ?", array($this->getId()));

        return $list->getData();
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
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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
     * @return bool
     */
    public function getHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param bool $highlight
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }

    /**
     * @return int
     */
    public function getUsagePerVoucherCode()
    {
        return $this->usagePerVoucherCode;
    }

    /**
     * @param int $usagePerVoucherCode
     */
    public function setUsagePerVoucherCode($usagePerVoucherCode)
    {
        $this->usagePerVoucherCode = $usagePerVoucherCode;
    }

    /**
     * @return boolean
     */
    public function getUseMultipleVoucherCodes()
    {
        return $this->useMultipleVoucherCodes;
    }

    /**
     * @param boolean $useMultipleVoucherCodes
     */
    public function setUseMultipleVoucherCodes($useMultipleVoucherCodes)
    {
        $this->useMultipleVoucherCodes = $useMultipleVoucherCodes;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->getName());
    }
}
