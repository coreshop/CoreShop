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

use Pimcore\Model;

class DiscountPercent extends AbstractAction {

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
    public $type = "discountPercent";

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
     * Calculate discount
     *
     * @param Model\Object\CoreShopCart $cart
     * @return int
     */
    public function getDiscount(Model\Object\CoreShopCart $cart) {
        return $cart->getSubtotal() * ($this->getPercent() / 100);
    }

    /**
     * Apply Rule to Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    public function applyRule(Model\Object\CoreShopCart $cart) {
        return true;
    }

    /**
     * Remove Rule from Cart
     *
     * @param Model\Object\CoreShopCart $cart
     * @return bool
     */
    public function unApplyRule(Model\Object\CoreShopCart $cart) {
        return true;
    }
}
