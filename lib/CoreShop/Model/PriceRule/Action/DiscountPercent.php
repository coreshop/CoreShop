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

namespace CoreShop\Model\PriceRule\Action;

use CoreShop\Model\Cart;
use CoreShop\Model\Currency;
use CoreShop\Model\Product;

/**
 * Class DiscountPercent
 * @package CoreShop\Model\PriceRule\Action
 */
class DiscountPercent extends AbstractAction
{
    /**
     * @var int
     */
    public $currency_id;

    /**
     * @var int
     */
    public $percent;

    /**
     * @var string
     */
    public $type = 'discountPercent';

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * @param mixed $currency_id
     */
    public function setCurrencyId($currency_id)
    {
        $this->currency_id = $currency_id;
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param int $percent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
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
        return $this->getDiscount($cart->getSubtotal($withTax));
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
        return $this->getDiscount($basePrice);
    }

    /**
     * Calculates the discount and converts it to a specific currency
     *
     * @param $price
     * @return mixed
     */
    protected function getDiscount($price)
    {
        $discount = $price * ($this->getPercent() / 100);

        return \CoreShop::getTools()->convertToCurrency($discount, \CoreShop::getTools()->getCurrency(), Currency::getById($this->getCurrencyId()));
    }
}
