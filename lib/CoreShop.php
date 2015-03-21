<?php
    
class CoreShop 
{
    /**
     * @var Zend_EventManager_EventManager
     */
    private static $eventManager;
    
    private static $layout = "layout";

    /**
     * @return Zend_EventManager_EventManager
     */
    public static function getEventManager() {
        if(!self::$eventManager) {
            self::$eventManager = new Zend_EventManager_EventManager();
        }
        return self::$eventManager;
    }
    
    public static function getLayout() {
        return self::$layout;
    }
    
    public static function setLayout($layout) {
        self::$layout = $layout;
    }
}