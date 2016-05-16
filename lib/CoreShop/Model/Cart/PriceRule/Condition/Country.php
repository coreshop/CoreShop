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
namespace CoreShop\Model\Cart\PriceRule\Condition;

use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Country as CountryModel;
use CoreShop\Tool;

class Country extends AbstractCondition
{
    /**
     * @var int
     */
    public $country;

    /**
     * @var string
     */
    public $type = 'country';

    /**
     * @return CountryModel
     */
    public function getCountry()
    {
        if (!$this->country instanceof CountryModel) {
            $this->country = CountryModel::getById($this->country);
        }

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
     * Check if Cart is Valid for Condition.
     *
     * @param Cart       $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkCondition(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        if ($this->getCountry()->getId() !== Tool::getCountry()->getId()) {
            if ($throwException) {
                throw new \Exception('You cannot use this voucher in your country of delivery');
            } else {
                return false;
            }
        }

        return true;
    }
}
