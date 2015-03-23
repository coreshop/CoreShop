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
    
    public static function getDeliveryProvider(Object_CoreShop_Cart $cart)
    {
        $results = self::getEventManager()->trigger("delivery.getProvider", null, array("cart" => $cart), function($v) {
            return ($v instanceof CoreShop_Interface_Delivery);
        });
        
        if($results->stopped())
        {
            $provider = array();
            
            foreach($results as $result)
            {
                $provider[] = $result;
            }
    
            return $provider;
        }
        
        return array();
    }
}