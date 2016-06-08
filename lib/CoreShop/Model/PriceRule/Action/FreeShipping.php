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
use CoreShop\Model\Product;


class FreeShipping extends AbstractAction
{
    /**
     * @var string
     */
    public $type = 'freeShipping';

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
     *
     * @return int
     */
    public function getDiscountCart(Cart $cart)
    {
        return 0;
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
}
