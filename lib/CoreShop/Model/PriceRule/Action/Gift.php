<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\PriceRule\Action;

use CoreShop\Model\PriceRule;
use CoreShop\Tool;

use Pimcore\Model\Object\CoreShopProduct;
use Pimcore\Model\Object\CoreShopCart;

class Gift extends AbstractAction {

    /**
     * @var int
     */
    public $gift;

    /**
     * @var string
     */
    public $type = "gift";

    /**
     * @return \Pimcore\Model\Object\CoreShopProduct
     */
    public function getGift()
    {
        if(!$this->gift instanceof CoreShopProduct)
            $this->gift = CoreShopProduct::getByPath($this->gift);

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
     * Calculate discount
     *
     * @param CoreShopCart $cart
     * @return int
     */
    public function getDiscount(CoreShopCart $cart)
    {
        $discount = Tool::convertToCurrency($this->getGift()->getProductPrice(), Tool::getCurrency());

        return $discount;
    }

    /**
     * Apply Rule to Cart
     *
     * @param CoreShopCart $cart
     * @return bool
     */
    public function applyRule(CoreShopCart $cart)
    {
        if($this->getGift() instanceof CoreShopProduct) {
            $item = $cart->updateQuantity($this->getGift(), 1, false);
            $item->setIsGiftItem(true);
            $item->save();
        }

        return true;
    }

    /**
     * Remove Rule from Cart
     *
     * @param CoreShopCart $cart
     * @return bool
     */
    public function unApplyRule(CoreShopCart $cart)
    {
        if($this->getGift() instanceof CoreShopProduct) {
            $cart->updateQuantity($this->getGift(), 0, false);
        }

        return true;
    }
}
