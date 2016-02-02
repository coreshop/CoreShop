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

namespace CoreShop\Model\TaxRule;

use CoreShop\Model\Configuration;
use CoreShop\Model\Country;
use CoreShop\Model\Plugin\TaxManager;
use CoreShop\Model\Tax;
use CoreShop\Model\TaxCalculator;
use CoreShop\Model\TaxRuleGroup;
use Pimcore\Model\Object\CoreShopUser;
use Pimcore\Model\Object\Fieldcollection\Data\CoreShopUserAddress;

class VatManager implements TaxManager
{
    /**
     * @var TaxCalculator
     */
    protected $tax_calculator;

    /**
     * @param CoreShopUserAddress $address
     * @param string $type
     * @return bool
     */
    public static function isAvailableForThisAddress(CoreShopUserAddress $address, $type)
    {
        if(Configuration::get("SYSTEM.BASE.DISABLEVATFORBASECOUNTRY")) {
            if(empty($address->getVatNumber())) {
                return false;
            }

            if($countryId = Configuration::get("SYSTEM.BASE.COUNTRY")) {
                if(($country = Country::getById($countryId)) instanceof Country) {
                    if($country->getId() !== $address->getCountry()->getId()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Return the tax calculator associated to this address
     *
     * @return TaxCalculator
     */
    public function getTaxCalculator()
    {
        if ($this->tax_calculator instanceof TaxCalculator) {
            return $this->tax_calculator;
        }

        $cacheKey = "coreshop_vattax_calculator";

        if (!\Zend_Registry::isRegistered($cacheKey)) {
            $tax = new Tax();
            $tax->setRate(0);

            \Zend_Registry::set($cacheKey, new TaxCalculator(array($tax), 0));
        }

        return \Zend_Registry::get($cacheKey);
    }
}
