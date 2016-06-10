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

class Quantity extends AbstractCondition
{
    /**
     * @var float
     */
    public $minQuantity;

    /**
     * @var float
     */
    public $maxQuantity;

    /**
     * @var string
     */
    public $type = 'quantity';

    /**
     * @return float
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * @param float $minQuantity
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;
    }

    /**
     * @return float
     */
    public function getMaxQuantity()
    {
        return $this->maxQuantity;
    }

    /**
     * @param float $maxQuantity
     */
    public function setMaxQuantity($maxQuantity)
    {
        $this->maxQuantity = $maxQuantity;
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
        //Check Cart Quantity
        if ($this->getMinQuantity() > 0) {
            $itemQuantityAmount = 0;

            foreach($cart->getItems() as $item) {
                $itemQuantityAmount += $item->getAmount();
            }

            if($itemQuantityAmount < $this->getMinQuantity()) {
                if ($throwException) {
                    throw new \Exception('You have not reached the minimum quantity required to use this voucher');
                } else {
                    return false;
                }
            }

            if($itemQuantityAmount >= $this->getMinQuantity()) {
                if($itemQuantityAmount > $this->getMaxQuantity()) {
                    if ($throwException) {
                        throw new \Exception('You have reached the maximum quantity required to use this voucher');
                    } else {
                        return false;
                    }
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
        //Check for Quantity in Cart
        $cart = Tool::prepareCart();

        if($cart instanceof Cart) {
            foreach($cart->getItems() as $item) {
                if($item->getProduct()->getId() === $product->getId() && $item->getAmount() >= $this->getMinQuantity()) {
                    if($this->getMaxQuantity() > 0) {
                        if($item->getAmount() <= $this->getMaxQuantity()) {
                            return true;
                        }
                    }
                    else {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
