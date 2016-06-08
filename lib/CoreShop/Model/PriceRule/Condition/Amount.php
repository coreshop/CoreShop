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

use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model;
use CoreShop\Tool;

class Amount extends AbstractCondition
{
    /**
     * @var int
     */
    public $currency;

    /**
     * @var float
     */
    public $minAmount;

    /**
     * @var string
     */
    public $type = 'amount';

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function getMinAmount()
    {
        return $this->minAmount;
    }

    /**
     * @param float $minAmount
     */
    public function setMinAmount($minAmount)
    {
        $this->minAmount = $minAmount;
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
        //Check Cart Amount
        if ($this->getMinAmount() > 0) {
            $minAmount = $this->getMinAmount();
            $minAmount = Tool::convertToCurrency($minAmount, $this->getCurrency(), Tool::getCurrency());

            $cartTotal = $cart->getSubtotal();

            if ($minAmount > $cartTotal) {
                if ($throwException) {
                    throw new \Exception('You have not reached the minimum amount required to use this voucher');
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param Model\Product $product
     * @param Model\Product\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(Model\Product $product, Model\Product\AbstractProductPriceRule $priceRule)
    {
        if($this->getMinAmount() > 1) {
            return false;
        }

        return true;
    }
}
