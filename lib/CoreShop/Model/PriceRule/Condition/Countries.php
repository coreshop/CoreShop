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

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;
use CoreShop\Model\Country as CountryModel;

/**
 * Class Countries
 * @package CoreShop\Model\PriceRule\Condition
 */
class Countries extends AbstractCondition
{
    /**
     * @var int[]
     */
    public $countries;

    /**
     * @var string
     */
    public $type = 'countries';

    /**
     * @return int[]
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @param int[] $countries
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
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
     * @throws Exception
     */
    public function checkConditionCart(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        return $this->check($throwException);
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param ProductModel $product
     * @param ProductModel\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
    {
        return $this->check();
    }

    /**
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    protected function check($throwException = false)
    {
        $found = false;

        if (is_array($this->getCountries())) {
            foreach ($this->getCountries() as $countryId) {
                if (intval($countryId) === intval(\CoreShop::getTools()->getCountry()->getId())) {
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            if ($throwException) {
                throw new Exception('You cannot use this voucher in your country of delivery');
            } else {
                return false;
            }
        }

        return true;
    }
}
