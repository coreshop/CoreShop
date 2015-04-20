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

namespace CoreShop;

use Pimcore\Model\Object\CoreShopCurrency;
use Pimcore\Model\Object\CoreShopCountry;

class Currency extends Base
{
    public static function getAvailable()
    {
        $countries = CoreShopCountry::getActiveCountries();

        $currencies = array();

        foreach($countries as $c)
        {
            if(!array_key_exists($c->getCurrency()->getKey(), $currencies))
                $currencies[$c->getCurrency()->getKey()] = $c->getCurrency();
        }

        return array_values($currencies);
    }
}