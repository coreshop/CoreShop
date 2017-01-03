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

namespace CoreShop\Model\TaxRule;

use CoreShop\Model\Configuration;
use CoreShop\Model\Country;
use CoreShop\Model\Plugin\TaxManager;
use CoreShop\Model\Tax;
use CoreShop\Model\TaxCalculator;
use CoreShop\Model\User\Address;

/**
 * Class VatManager
 * @package CoreShop\Model\TaxRule
 */
class VatManager implements TaxManager
{
    /**
     * @var TaxCalculator
     */
    protected $tax_calculator;

    /**
     * @param Address $address
     * @param string  $type
     *
     * @return bool
     */
    public static function isAvailableForThisAddress(Address $address, $type)
    {
        if (Configuration::get('SYSTEM.BASE.DISABLEVATFORBASECOUNTRY')) {
            if (empty($address->getVatNumber())) {
                return false;
            }

            if ($countryId = Configuration::get('SYSTEM.BASE.COUNTRY')) {
                if (($country = Country::getById($countryId)) instanceof Country) {
                    if ($country->getId() !== $address->getCountry()->getId()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Return the tax calculator associated to this address.
     *
     * @return TaxCalculator
     */
    public function getTaxCalculator()
    {
        if ($this->tax_calculator instanceof TaxCalculator) {
            return $this->tax_calculator;
        }

        $cacheKey = 'coreshop_vattax_calculator';

        if (!\Zend_Registry::isRegistered($cacheKey)) {
            $tax = Tax::create();
            $tax->setRate(0);

            \Zend_Registry::set($cacheKey, new TaxCalculator([$tax], 0));
        }

        return \Zend_Registry::get($cacheKey);
    }
}
