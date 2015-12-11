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

use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object;
use Pimcore\Mail;

use CoreShop\Model\Currency;
use CoreShop\Model\Country;
use CoreShop\Model\User;

use Pimcore\Tool\Session;

class Tool {

    /**
     * Format Price to locale
     *
     * @param $price
     * @return string
     */
    public static function formatPrice($price)
    {
        try
        {
            $zCurrency = new \Zend_Currency("de_DE"); //TODO: fix to use Zend_Locale
            return $zCurrency->toCurrency($price, array('symbol' => Tool::getCurrency()->getSymbol()));
        }
        catch(\Exception $ex)
        {
            echo $ex;
        }
        
        return $price;
    }

    /**
     * Converts value from currency to currency
     *
     * @param $value
     * @param Currency|null $toCurrency
     * @param Currency|null $fromCurrency
     * @return mixed
     */
    public static function convertToCurrency($value, Currency $toCurrency = null, Currency $fromCurrency = null)
    {
        $config = Config::getConfig();
        $configArray = $config->toArray();

        if(!$fromCurrency instanceof Currency)
            $fromCurrency= Currency::getById($configArray['base']['base-currency']);

        if(!$toCurrency instanceof Currency) {
            $toCurrency = Tool::getCurrency();
        }

        if($fromCurrency instanceof Currency) {
            if($toCurrency instanceof Currency && $toCurrency->getId() != $fromCurrency->getId()) {
                return $value * $toCurrency->getExchangeRate();
            }
        }

        return $value;
    }

    /**
     * Get CoreShop Session
     *
     * @return \stdClass
     */
    public static function getSession()
    {
        return Session::get('CoreShop');
    }

    /**
     * Get current User
     *
     * @return null|Object\User
     */
    public static function getUser() {
        $session = self::getSession();

        return $session->user instanceof User ? $session->user : null;
    }

    /**
     * Format Tax
     *
     * TODO: Localization
     *
     * @param $tax
     * @return string
     */
    public static function formatTax($tax)
    {
        return ($tax * 100) . "%";
    }

    /**
     * Prepare Cart
     *
     * @return CoreShopCart|static
     */
    public static function prepareCart()
    {
        $cartSession = self::getSession();

        if($cartSession->cartId)
        {
            $cart = CoreShopCart::getById($cartSession->cartId);

            if($cart instanceof CoreShopCart)
                return $cart;
        }

        $cart = CoreShopCart::prepare();
        $cartSession->cartId = $cart->getId();

        return $cart;
    }

    /**
     * Get current Users Country
     *
     * @return Country|null
     * @throws \Exception
     */
    public static function getCountry()
    {
        $session = self::getSession();
        $country = null;

        if($session->countryId) {
            $country = Country::getById($session->countryId);

            if ($country instanceof Country)
                return $country;
        }


        if (self::getSession()->user instanceof User) {
            $user = self::getSession()->user;

            if (count($user->getAddresses()) > 0)
                $country = $user->getAddresses()->get(0);
        }

        if (!$country instanceof Country) {
            if(file_exists(CORESHOP_CONFIGURATION_PATH . "/GeoIP/GeoIP.dat")) {
                $gi = geoip_open(CORESHOP_CONFIGURATION_PATH . "/GeoIP/GeoIP.dat", GEOIP_MEMORY_CACHE);

                $country = geoip_country_code_by_addr($gi, \Pimcore\Tool::getClientIp());

                geoip_close($gi);

                $country = Country::getByIsoCode($country);
            }
            else
            {
                $enabled = Country::getActiveCountries();

                if(count($enabled) > 0)
                    return $enabled[0];
                else
                {
                    throw new \Exception("no enabled countries found");
                }
            }
        }


        if(!$country instanceof Country) {

            //Using Default Country: AT
            //TODO: Default Country configurable thru settings
            $country = Country::getById(7);
            //throw new \Exception("Country with code $country not found");
        }

        $session->countryId = $country->getId();

        return $country;
    }

    /**
     * Get current Currency by Country
     *
     * @return Currency|null
     * @throws \Exception
     */
    public static function getCurrency()
    {
        $session = self::getSession();

        if($session->currencyId)
        {
            $currency = Currency::getById($session->currencyId);

            if($currency instanceof Currency)
                return $currency;
        }


        return self::getCountry()->getCurrency();
    }

    /**
     * Check if Object $object in array $objectList
     *
     * @param AbstractObject $object
     * @param array $objectList
     * @return bool
     */
    public static function objectInList(AbstractObject $object, array $objectList)
    {
        foreach($objectList as $o) {
            if($o->getId() == $object->getId())
                return true;
        }

        return false;
    }
    
    /**
     * Retreive the values in an array
     *
     * @return array
     */
    public static function objectToArray(Object\Concrete $object)
    {
        return self::_objectToArray($object);
    }

    /**
     * Retreive the values in json format
     *
     * @return string
     */
    public static function objectToJson(Object\Concrete $object)
    {
        return \Zend_Json::encode(self::_objectToArray($object));
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
                    if(($value instanceof Object\Fieldcollection) && is_array($value->getItems()))
                    {
                        /** @var $value Object_Fieldcollection */
                        $def = $value->getItemDefinitions();
                        if(method_exists($def['children'], 'getFieldDefinitions'))
                            $collection[$fieldName] = self::_objectToArray($value->getItems(), $def['children']->getFieldDefinitions());
                    }
                    break;

                case 'date':
                    /** @var $value Pimcore_Date */
                    $collection[$fieldName] = ($value instanceof \Pimcore\Date) ? $value->getTimestamp() : 0;
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
    
    /*
     * Class Mapping Tools
     * They are used to map some instances of CoreShop_base to an defined class (type)
     */

    /**
     * @static
     * @param  $sourceClassName
     * @return string
     */
    public static function getModelClassMapping($sourceClassName, $interfaceToImplement = null) {

        $targetClassName = $sourceClassName;
        
        if(!$interfaceToImplement)
            $interfaceToImplement = $targetClassName;

        if($map = \CoreShop\Config::getModelClassMappingConfig()) {
            $tmpClassName = $map->{$sourceClassName};
            
            if($tmpClassName)  {
                if(\Pimcore\Tool::classExists($tmpClassName)) {
                    if(is_subclass_of($tmpClassName, $interfaceToImplement)) {
                        $targetClassName = $tmpClassName;
                    } else {
                        \Logger::error("Classmapping for " . $sourceClassName . " failed. '" . $tmpClassName . " is not a subclass of '" . $interfaceToImplement . "'. " . $tmpClassName . " has to extend " . $interfaceToImplement);
                    }
                } else {
                    \Logger::error("Classmapping for " . $sourceClassName . " failed. Cannot find class '" . $tmpClassName . "'");
                }
            }
        }

        return $targetClassName;
    }

    /**
     * Add all Users to mail
     *
     * TODO: Use Users from Pimcore
     *
     * @param Mail $mail
     */
    public static function addAdminToMail(Mail $mail) {
        $mail->addBcc("dominik@pfaffenbauer.at");
    }
}
