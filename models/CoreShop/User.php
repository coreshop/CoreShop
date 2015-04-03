<?php

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