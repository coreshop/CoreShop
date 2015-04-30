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

namespace CoreShop\Model;

use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;
use Pimcore\Model\Object\CoreShopCountry;

class CountryModel extends Base {

    public static function getActiveCountries()
    {
        $list = new CoreShopCountry\Listing();
        $list->setCondition("active = 1");

        return $list->getObjects();
    }
}