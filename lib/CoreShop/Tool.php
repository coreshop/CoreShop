<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use CoreShop\Model\AbstractModel;
use Pimcore\Date;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object;
use Pimcore\Mail;

use CoreShop\Model\Currency;
use CoreShop\Model\Country;
use CoreShop\Model\User;

use Pimcore\Tool\Session;

use GeoIp2\Database\Reader;


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
    public static function convertToCurrency($value, $toCurrency = null, $fromCurrency = null)
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
     * @return Session
     */
    public static function getSession()
    {
        return Session::get('CoreShop');
    }

    /**
     * Get current User
     *
     * @return null|User
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

            $geoDbFile = realpath(PIMCORE_WEBSITE_VAR . "/config/GeoLite2-City.mmdb");
            $record = null;

            if(file_exists($geoDbFile)) {
                try {
                    $reader = new Reader($geoDbFile);

                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }

                    if(!self::checkIfIpIsPrivate($ip)) {
                        $record = $reader->city($ip);

                        $country = Country::getByIsoCode($record->country->isoCode);
                    }
                } catch (\Exception $e) {
                    
                }
            }
        }


        if(!$country instanceof Country) {

            //Using Default Country: AT
            //TODO: Default Country configurable thru settings
            $country = Country::getById(2);
            //throw new \Exception("Country with code $country not found");
        }

        $session->countryId = $country->getId();

        return $country;
    }

    /**
     * Check if ip is private
     *
     * @param $ip
     * @return bool
     */
    private static function checkIfIpIsPrivate ($ip) {
        $pri_addrs = array (
            '10.0.0.0|10.255.255.255', // single class A network
            '172.16.0.0|172.31.255.255', // 16 contiguous class B network
            '192.168.0.0|192.168.255.255', // 256 contiguous class C network
            '169.254.0.0|169.254.255.255', // Link-local address also refered to as Automatic Private IP Addressing
            '127.0.0.0|127.255.255.255' // localhost
        );

        $long_ip = ip2long ($ip);
        if ($long_ip != -1) {

            foreach ($pri_addrs AS $pri_addr) {
                list ($start, $end) = explode('|', $pri_addr);

                // IF IS PRIVATE
                if ($long_ip >= ip2long ($start) && $long_ip <= ip2long ($end)) {
                    return true;
                }
            }
        }

        return false;
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
     * @param AbstractModel $object
     * @param array $objectList
     * @return bool
     */
    public static function objectInList(AbstractModel $object, array $objectList)
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
     * @param $object
     * @return array
     */
    public static function objectToArray(Object\Concrete $object)
    {
        return self::_objectToArray($object);
    }

    /**
     * Retreive the values in json format
     *
     * @param $object
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
                        /** @var $value Object\Fieldcollection */
                        $def = $value->getItemDefinitions();
                        if(method_exists($def['children'], 'getFieldDefinitions'))
                            $collection[$fieldName] = self::_objectToArray($value->getItems(), $def['children']->getFieldDefinitions());
                    }
                    break;

                case 'date':
                    /** @var $value \Pimcore\Date */
                    $collection[$fieldName] = ($value instanceof Date) ? $value->getTimestamp() : 0;
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
