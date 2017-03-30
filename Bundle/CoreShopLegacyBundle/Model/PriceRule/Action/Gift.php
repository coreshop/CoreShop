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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Action;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;

/**
 * Class Gift
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Action
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
     * @return int
     */
    public function getGift()
    {
        return $this->gift;
    }

    /**
     * @param int $gift
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
        $giftProduct = Product::getById($this->getGift());

        if ($giftProduct instanceof Product) {
            $item = $cart->updateQuantity($giftProduct, 1, false, false);
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
        $giftProduct = Product::getById($this->getGift());

        if ($giftProduct instanceof Product) {
            $cart->updateQuantity($giftProduct, 0, false, false);
        }

        return true;
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
        $giftProduct = Product::getById($this->getGift());

        if ($giftProduct instanceof Product) {
            return $giftProduct->getPrice($withTax);
        }

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
