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
use CoreShop\Model\Cart;
use CoreShop\Model\Configuration;
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

class Tool
{

    /**
     * Format Price to locale
     *
     * @param $price
     * @return string
     */
    public static function formatPrice($price)
    {
        try {
            $zCurrency = new \Zend_Currency("de_DE"); //TODO: fix to use Zend_Locale
            return $zCurrency->toCurrency($price, array('symbol' => Tool::getCurrency()->getSymbol()));
        } catch (\Exception $ex) {
            echo $ex;
        }
        
        return $price;
    }
    
    /**
     * Round Price
     *
     * @param $price
     * @return float
     */
    public static function roundPrice($price)
    {
        return round($price, 2);
    }

    /**
     * Format Number without thousands seperator (eg 1540,32)
     *
     * @param $number
     * @return string
     */
    public static function numberFormat($number, $decimalPrecision = 2)
    {
        return number_format($number, $decimalPrecision, ',', '');
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
        if (!$fromCurrency instanceof Currency) {
            $fromCurrency = self::getBaseCurrency();
        }

        if (!$toCurrency instanceof Currency) {
            $toCurrency = Tool::getCurrency();
        }

        if ($fromCurrency instanceof Currency) {
            if ($toCurrency instanceof Currency && $toCurrency->getId() != $fromCurrency->getId()) {
                return $value * $toCurrency->getExchangeRate();
            }
        }

        return $value;
    }

    /**
     * get base Currency
     *
     * @return Currency
     */
    public static function getBaseCurrency()
    {
        $baseCurrency = Configuration::get("SYSTEM.BASE.CURRENCY");
        $currency = null;

        if ($baseCurrency) {
            $currency = Currency::getById($baseCurrency);
        }

        if (!$currency instanceof Currency) {
            \Logger::warn("No SYSTEM.BASE.CURRENCY found, so EURO is going to be used! Please set your Default Currency in CoreShop Settings");

            $currency = Currency::getById(1); //TODO: Throw Exception because there is no base currency?
        }

        return $currency;
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
    public static function getUser()
    {
        $session = self::getSession();

        return $session->user instanceof User ? $session->user : null;
    }

    /**
     * Load Controller from CoreShop
     * @param string $controllerName
     */
    public static function loadController($controllerName = '')
    {
        if (file_exists(CORESHOP_PATH . "/controllers/" . $controllerName . "Controller.php")) {
            require(CORESHOP_PATH . "/controllers/" . $controllerName . "Controller.php");
        }
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
     * @param $resetCart bool create a new cart
     * @return Cart
     */
    public static function prepareCart($resetCart = false)
    {
        $cartSession = self::getSession();

        $cart = NULL;

        if (!$resetCart) {
            if (isset($cartSession->cartId) && $cartSession->cartId !== 0) {
                $cart = CoreShopCart::getById($cartSession->cartId);
            } else if(isset($cartSession->cartObj)) {
                if ($cartSession->cartObj instanceof CoreShopCart) {
                    $cart = $cartSession->cartObj;

                    if( $cart->getId() !== 0) {
                        unset( $cartSession->cartObj );
                        $cartSession->cartId = $cart->getId();
                    }

                }
            }
        }

        if ($cart instanceof CoreShopCart) {
            if($cart->getUser() === null && count($cart->getItems()) && self::getUser() instanceof User) {
                $cart->setUser(self::getUser());
                $cart->save();
            }

            return $cart;
        }

        $cart = CoreShopCart::prepare();
        $cartSession->cartObj = $cart;

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
        $cart = self::prepareCart();

        if ($session->countryId) {
            $country = Country::getById($session->countryId);

            if ($country instanceof Country) {
                return $country;
            }
        }

        if ($cart instanceof Cart) {
            if (count($cart->getBillingAddress()) > 0) {
                $address = $cart->getBillingAddress()->get(0);

                if ($address instanceof User\Address) {
                    $country = $address->getCountry();
                }
            }
        }

        if (!$country instanceof Country) {
            if ($session->user instanceof User) {
                $user = $session->user;

                if (count($user->getAddresses()) > 0) {
                    $country = $user->getAddresses()->get(0)->getCountry();
                }
            }
        }

        if (!$country instanceof Country) {
            $geoDbFile = realpath(PIMCORE_WEBSITE_VAR . "/config/GeoLite2-City.mmdb");
            $record = null;

            if (file_exists($geoDbFile)) {
                try {
                    $reader = new Reader($geoDbFile);

                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }

                    if (!self::checkIfIpIsPrivate($ip)) {
                        $record = $reader->city($ip);

                        $country = Country::getByIsoCode($record->country->isoCode);
                    }
                } catch (\Exception $e) {
                }
            }
        }


        if (!$country instanceof Country) {
            $country = Plugin::actionHook("country");

            if (!$country instanceof Country) {
                $country = Country::getById(Configuration::get("SYSTEM.BASE.COUNTRY"));

                if (!$country instanceof Country) {
                    \Logger::warn("No SYSTEM.BASE.COUNTRY found, so AUSTRIA is going to be used! Please set your Default Country in CoreShop Settings");
                    $country = Country::getById(2);
                }
            }
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
    private static function checkIfIpIsPrivate($ip)
    {
        $pri_addrs = array(
            '10.0.0.0|10.255.255.255', // single class A network
            '172.16.0.0|172.31.255.255', // 16 contiguous class B network
            '192.168.0.0|192.168.255.255', // 256 contiguous class C network
            '169.254.0.0|169.254.255.255', // Link-local address also refered to as Automatic Private IP Addressing
            '127.0.0.0|127.255.255.255' // localhost
        );

        $long_ip = ip2long($ip);
        if ($long_ip != -1) {
            foreach ($pri_addrs as $pri_addr) {
                list($start, $end) = explode('|', $pri_addr);

                // IF IS PRIVATE
                if ($long_ip >= ip2long($start) && $long_ip <= ip2long($end)) {
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

        if ($session->currencyId) {
            $currency = Currency::getById($session->currencyId);

            if ($currency instanceof Currency) {
                return $currency;
            }
        }

        if (self::getCountry()->getCurrency() instanceof Currency) {
            return self::getCountry()->getCurrency();
        }

        return self::getBaseCurrency();
    }

    /**
     * Validate VAT for address
     *
     * @param string $vatNubmer
     * @return boolean
     */
    public static function validateVatNumber($vatNumber)
    {
        $intracom_array = array(
            'AT' => 'AT',
            //Austria
            'BE' => 'BE',
            //Belgium
            'DK' => 'DK',
            //Denmark
            'FI' => 'FI',
            //Finland
            'FR' => 'FR',
            //France
            'FX' => 'FR',
            //France métropolitaine
            'DE' => 'DE',
            //Germany
            'GR' => 'EL',
            //Greece
            'IE' => 'IE',
            //Irland
            'IT' => 'IT',
            //Italy
            'LU' => 'LU',
            //Luxembourg
            'NL' => 'NL',
            //Netherlands
            'PT' => 'PT',
            //Portugal
            'ES' => 'ES',
            //Spain
            'SE' => 'SE',
            //Sweden
            'GB' => 'GB',
            //United Kingdom
            'CY' => 'CY',
            //Cyprus
            'EE' => 'EE',
            //Estonia
            'HU' => 'HU',
            //Hungary
            'LV' => 'LV',
            //Latvia
            'LT' => 'LT',
            //Lithuania
            'MT' => 'MT',
            //Malta
            'PL' => 'PL',
            //Poland
            'SK' => 'SK',
            //Slovakia
            'CZ' => 'CZ',
            //Czech Republic
            'SI' => 'SI',
            //Slovenia
            'RO' => 'RO',
            //Romania
            'BG' => 'BG',
            //Bulgaria
            'HR' => 'HR',
            //Croatia
        );

        $vatNumber = str_replace(' ', '', $vatNumber);
        $prefix = substr($vatNumber, 0, 2);

        if (array_search($prefix, $intracom_array) === false) {
            return false;
        }

        $vat = substr($vatNumber, 2);
        $url = 'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms='.urlencode($prefix).'&iso='.urlencode($prefix).'&vat='.urlencode($vat);

        for ($i = 0; $i < 3; $i++) {
            if ($page_res = @file_get_contents($url)) {
                if (preg_match('/invalid VAT number/i', $page_res)) {
                    return false;
                } elseif (preg_match('/valid VAT number/i', $page_res)) {
                    return true;
                } else {
                    $i++;
                }
            } else {
                sleep(1);
            }
        }

        return false;
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
        foreach ($objectList as $o) {
            if ($o->getId() == $object->getId()) {
                return true;
            }
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
        if (is_array($object)) {
            $collections = array();
            foreach ($object as $o) {
                $collections[] = self::_objectToArray($o, $fieldDefintions);
            }
            return $collections;
        }
        if (!is_object($object)) {
            return false;
        }

        //Custom list field definitions
        if (null === $fieldDefintions) {
            $fieldDefintions = $object->getClass()->getFieldDefinitions();
        }

        $collection = array();
        foreach ($fieldDefintions as $fd) {
            $fieldName = $fd->getName();
            $getter    = "get" . ucfirst($fieldName);
            $value     = $object->$getter();

            switch ($fd->getFieldtype()) {
                case 'fieldcollections':
                    if (($value instanceof Object\Fieldcollection) && is_array($value->getItems())) {
                        /** @var $value Object\Fieldcollection */
                        $def = $value->getItemDefinitions();
                        if (method_exists($def['children'], 'getFieldDefinitions')) {
                            $collection[$fieldName] = self::_objectToArray($value->getItems(), $def['children']->getFieldDefinitions());
                        }
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
     * get CoreShop Translate
     *
     * @return \Zend_Translate_Adapter
     */
    public static function getTranslate() {
        $lang = null;
        $user = \Pimcore\Tool\Admin::getCurrentUser();

        if($user instanceof User) {
            $lang = $user->getLanguage();
        }
        else {
            $lang = \Zend_Registry::get("Zend_Locale");
        }

        if(!$lang)
            $lang = \Pimcore\Tool::getDefaultLanguage();

        return Plugin::getTranslate($lang);
    }
}
