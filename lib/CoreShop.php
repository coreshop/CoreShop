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
    
    public static function getDeliveryProviders(Object_CoreShopCart $cart)
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
    
    public static function getDeliveryProvider($identifier)
    {
        $results = self::getEventManager()->trigger("delivery.getProvider", null, array("cart" => $cart), function($v) {
            return ($v instanceof CoreShop_Interface_Delivery && $v->getIdentifier() == $identifier);
        });
        
        if($results->stopped())
        {
            return $results->last();
        }
        
        return false;
    }
    
    public static function getPaymentProviders(Object_CoreShopCart $cart)
    {
        $results = self::getEventManager()->trigger("payment.getProvider", null, array("cart" => $cart), function($v) {
            return ($v instanceof CoreShop_Interface_Payment);
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
    
    public static function getPaymentProvider($identifier)
    {
        $providers = self::getPaymentProviders(null);
        
        foreach($providers as $provider)
        {
            if($provider->getIdentifier() == $identifier)
                return $provider;
        }
        
        return false;
    }
}