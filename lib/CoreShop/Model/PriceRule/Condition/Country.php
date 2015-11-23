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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Model\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Country as CountryModel;
use CoreShop\Tool;

class Country extends AbstractCondition {

    /**
     * @var int
     */
    public $country;

    /**
     * @var string
     */
    public $type = "country";

    /**
     * @return int
     */
    public function getCountry()
    {
        if(!$this->country instanceof CountryModel)
            $this->country = CountryModel::getById($this->country);

        return $this->country;
    }

    /**
     * @param int $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Check if Cart is Valid for Condition
     *
     * @param Cart $cart
     * @param PriceRule $priceRule
     * @param bool|false $throwException
     * @return bool
     * @throws \Exception
     */
    public function checkCondition(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        if($this->getCountry()->getId() !== Tool::getCountry()->getId())
        {
            if($throwException) throw new \Exception("You cannot use this voucher in your country of delivery"); else return false;
        }

        return true;
    }
}
