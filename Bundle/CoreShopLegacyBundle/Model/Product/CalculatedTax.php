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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Configuration;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Country;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address;
use Pimcore\Model\Object\Concrete;

/**
 * Class CalculatedTax
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Product
 */
class CalculatedTax
{
    /**
     * @param $object Concrete
     * @param $context \Pimcore\Model\Object\Data\CalculatedValue
     *
     * @return string
     */
    public static function compute($object, $context)
    {
        if ($object instanceof Product) {
            $price = $object->getRetailPrice();

            $address = Address::create();
            $address->setCountry(Country::getById(Configuration::get('SYSTEM.BASE.COUNTRY')));

            $calculator = $object->getTaxCalculator($address);

            if ($calculator) {
                return $calculator->addTaxes($price);
            }

            return $price;
        }

        return 0;
    }
}
