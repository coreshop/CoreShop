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
use CoreShop\Model\Product;

/**
 * Class Gift
 * @package CoreShop\Model\PriceRule\Action
 */
class Gift extends AbstractAction
{
    /**
     * @var string
     */
    public static $type = 'gift';

    /**
     * @var int
     */
    public $gift;

    /**
     * @return Product
     */
    public function getGift()
    {
        if (!$this->gift instanceof Product) {
            $this->gift = Product::getById($this->gift);
        }

        return $this->gift;
    }

    /**
     * @param Product $gift
     */
    public function setGift($gift)
    {
        $this->gift = $gift;
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
        if ($this->getGift() instanceof Product) {
            $item = $cart->updateQuantity($this->getGift(), 1, false, false);
            $item->setIsGiftItem(true);
            $item->save();
        }

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
        if ($this->getGift() instanceof Product) {
            $cart->updateQuantity($this->getGift(), 0, false, false);
        }

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
        $discount = $this->getGift()->getPrice($withTax);

        return $discount;
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
