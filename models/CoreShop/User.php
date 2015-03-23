<?php
    
class CoreShop_User extends CoreShop_Base
{
    public static function create()
    {
        $class = self::getUserClass();
        
        return new $class();
    }
    
    public static function getUserClass()
    {
        return CoreShop_Tool::getModelClassMapping("CoreShop_User", "CoreShop_Interface_User");
    }

    public static function __callStatic($name, $arguments)
    {
        $class = self::getUserClass();
        
        return call_user_func(array($class, $name), $arguments);
    }
}