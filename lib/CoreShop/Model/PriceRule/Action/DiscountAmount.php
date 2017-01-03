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

namespace CoreShop\Model\PriceRule\Action;

use CoreShop\Model\Cart;
use CoreShop\Model\Currency;
use CoreShop\Model\Product;

/**
 * Class DiscountAmount
 * @package CoreShop\Model\PriceRule\Action
 */
class DiscountAmount extends AbstractAction
{
    /**
     * @var string
     */
    public static $type = 'discountAmount';

    /**
     * @var int
     */
    public $currency;

    /**
     * @var float
     */
    public $amount;

    /**
     * @return int
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param int $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Apply Rule to Cart.
     *
     * @param Cart $cart
     *
     * @return bool
     */
    public function applyRule(Cart $cart)
    {
        return true;
    }

    /**
     * Remove Rule from Cart.
     *
     * @param Cart $cart
     *
     * @return bool
     */
    public function unApplyRule(Cart $cart)
    {
        return true;
    }

    /**
     * Calculate discount.
     *
     * @param Cart $cart
     * @param boolean $withTax
     *
     * @return int
     */
    public function getDiscountCart(Cart $cart, $withTax = true)
    {
        $amount = \CoreShop::getTools()->convertToCurrency($this->getAmount(), $cart->getCurrency(), Currency::getById($this->getCurrency()));

        if ($withTax) {
            $subTotalTe = $cart->getSubtotal(false);
            $subTotalTax = $cart->getSubtotalTax();

            $cartAverageTax = $subTotalTax / $subTotalTe;

            $amount *= 1 + $cartAverageTax;
        }

        return $amount;
    }

    /**
     * Calculate discount.
     *
     * @param float   $basePrice
     * @param Product $product
     *
     * @return float
     */
    public function getDiscountProduct($basePrice, Product $product)
    {
        return \CoreShop::getTools()->convertToCurrency($this->getAmount(), \CoreShop::getTools()->getCurrency(), Currency::getById($this->getCurrency()));
    }
}
