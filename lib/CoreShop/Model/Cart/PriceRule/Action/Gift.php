<?php
/**
 * CoreShop
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

namespace CoreShop\Model\Cart\PriceRule\Action;

use CoreShop\Model\Cart;
use CoreShop\Model\Cart\PriceRule\Action\AbstractAction;
use CoreShop\Model\Product;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Tool;

class Gift extends AbstractAction
{

    /**
     * @var Product
     */
    public $gift;

    /**
     * @var string
     */
    public $type = "gift";

    /**
     * @return Product
     */
    public function getGift()
    {
        if (!$this->gift instanceof Product) {
            $this->gift = Product::getByPath($this->gift);
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
     * Calculate discount
     *
     * @param Cart $cart
     * @return int
     */
    public function getDiscount(Cart $cart)
    {
        $discount = Tool::convertToCurrency($this->getGift()->getPrice(), Tool::getCurrency());

        return $discount;
    }

    /**
     * Apply Rule to Cart
     *
     * @param Cart $cart
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
     * Remove Rule from Cart
     *
     * @param Cart $cart
     * @return bool
     */
    public function unApplyRule(Cart $cart)
    {
        if ($this->getGift() instanceof Product) {
            $cart->updateQuantity($this->getGift(), 0, false, false);
        }

        return true;
    }
}
