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
namespace CoreShop\Model\PriceRule\Action;

use CoreShop\Model\Cart;
use CoreShop\Model\PriceRule\AbstractActionCondition;
use CoreShop\Model\Product;

abstract class AbstractAction extends AbstractActionCondition
{
    /**
     * @var string
     */
    public $elementType = 'action';

    /**
     * Apply Rule to Cart.
     *
     * @param Cart $cart
     *
     * @return bool
     */
    abstract public function applyRule(Cart $cart);

    /**
     * Remove Rule from Cart.
     *
     * @param Cart $cart
     *
     * @return bool
     */
    abstract public function unApplyRule(Cart $cart);

    /**
     * Calculate discount.
     *
     * @param Cart $cart
     *
     * @return int
     */
    abstract public function getDiscountCart(Cart $cart);

    /**
     * Calculate discount.
     *
     * @param float   $basePrice
     * @param Product $product
     *
     * @return float
     */
    abstract public function getDiscountProduct($basePrice, Product $product);

    /**
     * get new price for product.
     *
     * @param Product $product
     *
     * @return float|boolean $price
     */
    public function getPrice(Product $product) {
        return false;
    }
}
