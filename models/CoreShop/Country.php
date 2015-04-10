<?php

namespace CoreShop;

use CoreShop\Base;
use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;

class Country extends Base {

    public static function getActiveCountries()
    {
        $list = new \Pimcore\Model\Object\CoreShopCountry\Listing();
        $list->setCondition("active = 1");

        return $list->getObjects();
    }
}