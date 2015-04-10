<?php

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