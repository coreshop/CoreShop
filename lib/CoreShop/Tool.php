<?php

class CoreShop_Tool {
    
    public static function formatPrice($price, $currency = "EUR")
    {
        $zCurrency = new Zend_Currency(Zend_Locale::getLocaleToTerritory(Zend_Registry::get("Zend_Locale")));

        return $zCurrency->toCurrency($price, array('currency' => $zCurrency));
    }
    
    public static function formatTax($tax)
    {
        return ($tax * 100) . "%";
    }
    
    public static function prepareCart()
    {
        $cartSession = Pimcore_Tool_Session::get('CoreShop');

        if($cartSession->cartId)
        {
            $cart = Object_CoreShopCart::getById($cartSession->cartId);

            if($cart instanceof CoreShop_Cart)
                return $cart;
        }
        
        $cart = CoreShop_Cart::create();
        $cartSession->cartId = $cart->getId();
        
        return $cart;
    }
    
    /**
     * Retreive the values in an array
     *
     * @return array
     */
    public static function objectToArray(Object_Concrete $object)
    {
        return self::_objectToArray($object);
    }

    /**
     * Retreive the values in json format
     *
     * @return string
     */
    public static function objectToJson(Object_Concrete $object)
    {
        return Zend_Json::encode(self::_objectToArray($object));
    }

    /**
     * Re-usable helper method
     * @todo move to the library helpers
     *
     * @static
     * @param $object
     * @param null $fieldDefintions
     * @return array
     */
    protected static function _objectToArray($object, $fieldDefintions=null)
    {
        //if the given object is an array then loop through each element
        if(is_array($object))
        {
            $collections = array();
            foreach($object as $o)
            {
                $collections[] = self::_objectToArray($o, $fieldDefintions);
            }
            return $collections;
        }
        if(!is_object($object)) return false;

        //Custom list field definitions
        if(null === $fieldDefintions)
        {
            $fieldDefintions = $object->getClass()->getFieldDefinitions();
        }

        $collection = array();
        foreach($fieldDefintions as $fd)
        {
            $fieldName = $fd->getName();
            $getter    = "get" . ucfirst($fieldName);
            $value     = $object->$getter();

            switch($fd->getFieldtype())
            {
                case 'fieldcollections':
                    if(($value instanceof Object_Fieldcollection) && is_array($value->getItems()))
                    {
                        /** @var $value Object_Fieldcollection */
                        $def = $value->getItemDefinitions();
                        $collection[$fieldName] = self::_objectToArray($value->getItems(), $def['children']->getFieldDefinitions());
                    }
                    break;

                case 'date':
                    /** @var $value Pimcore_Date */
                    $collection[$fieldName] = ($value instanceof Pimcore_Date) ? $value->getTimestamp() : 0;
                    break;
                default:
                    /** @var $value string */
                    $collection[$fieldName] = $value;
            }
        }

        //Parent class properties
        $collection['id']  = $object->o_id;
        $collection['key'] = $object->o_key;
        return $collection;
    }
}
