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
 * Class NewPrice
 * @package CoreShop\Model\PriceRule\Action
 */
class NewPrice extends AbstractAction
{
    /**
     * @var float
     */
    public $newPrice;

    /**
     * @var int
     */
    public $currency;

    /**
     * @var string
     */
    public $type = 'newPrice';

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
     * @param float   $basePrice
     * @param Product $product
     *
     * @return float
     */
    public function getDiscountProduct($basePrice, Product $product)
    {
        return 0;
    }

    /**
     * Calculate discount.
     *
     * @param Cart $cart
     * @param boolean $withTax
     *
     * @return float
     */
    public function getDiscountCart(Cart $cart, $withTax = true)
    {
        return 0;
    }

    /**
     * get new price for product.
     *
     * @param Product $product
     *
     * @return float $price
     */
    public function getPrice(Product $product)
    {
        return \CoreShop::getTools()->convertToCurrency($this->getNewPrice(), \CoreShop::getTools()->getCurrency(), Currency::getById($this->getCurrency()));
    }

    /**
     * get new price with tax for product
     *
     * @param Product $product
     *
     * @returns float
     */
    public function getPriceWithTax(Product $product)
    {
        $taxCalculator = $product->getTaxCalculator();
        $price = $this->getNewPrice();

        if ($taxCalculator) {
            $price = $taxCalculator->addTaxes($price);
        }

        return $price;
    }

    /**
     * @return float
     */
    public function getNewPrice()
    {
        return $this->newPrice;
    }

    /**
     * @param float $newPrice
     */
    public function setNewPrice($newPrice)
    {
        $this->newPrice = $newPrice;
    }

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
}
