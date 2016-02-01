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


namespace CoreShop\Model\Product;

use CoreShop\Model\Configuration;
use CoreShop\Model\Country;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object\CoreShopProduct;

class CalculatedTax {
    /**
     * @param $object Concrete
     * @param $context \Pimcore\Model\Object\Data\CalculatedValue
     * @return string
     */
    public static function compute($object, $context)
    {
        if($object instanceof CoreShopProduct) {
            $price = $object->getRetailPrice();
            $calculator = $object->getTaxCalculator(Country::getById(Configuration::get("SYSTEM.BASE.COUNTRY")));

            if ($calculator) {
                return $calculator->addTaxes($price);
            }

            return $price;
        }

        return 0;
    }
}