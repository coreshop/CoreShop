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

use CoreShop\Base;
use CoreShop\Tool;

class User extends Base
{
    public static function create()
    {
        $class = self::getUserClass();

        return new $class();
    }

    public static function getUserClass()
    {
        $class = Tool::getModelClassMapping("Pimcore\Model\Object\CoreShopUser", "CoreShop\Plugin\User");

        return $class;
    }

    public static function __callStatic($name, $arguments)
    {
        $class = self::getUserClass();

        return call_user_func_array(array($class, $name), $arguments);
    }
}