<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use CoreShop\Model\Cart;
use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use CoreShop\Model\Shop;
use CoreShop\Model\Visitor;
use Pimcore\Logger;
use Pimcore\Model\Object;
use CoreShop\Model\Currency;
use CoreShop\Model\Country;
use CoreShop\Model\User;
use Pimcore\Tool;
use Pimcore\Tool\Session;
use GeoIp2\Database\Reader;
use Pimcore\View\Helper\Url;

/**
 * Class Tools
 * @package CoreShop
 */
class Tools
{
    /**
     * @var Url
     */
    protected $urlViewHelper;

    /**
     * Tools constructor.
     */
    public function __construct()
    {
        $this->urlViewHelper = new Url();
    }


    /**
     * assembles an url
     *
     * @param $userParams
     * @param null $name
     * @param bool $reset
     * @param bool $encode
     * @return string
     */
    public function url($userParams = [], $name = null, $reset = false, $encode = true) {

        $results = \Pimcore::getEventManager()->trigger("coreshop.url", $this, [
            "name" => $name,
            "params" => $userParams,
            "reset" => $reset,
            "encode" => $encode
        ]);

        if ($results->count()) {
            $userParams = $results->last();
        }

        return $this->urlViewHelper->url($userParams, $name, $reset, $encode);
    }

    /**
     * @return string
     */
    public function getFingerprint()
    {
        $user = $this->getUser();
        $fingerprint = "";

        if ($user instanceof User) {
            $fingerprint .= $user->getCacheKey();
        }

        if ($this->prepareCart()->getId()) {
            $fingerprint .= $this->prepareCart()->getCacheKey();
        }

        if ($this->getDeliveryAddress() instanceof User\Address) {
            $fingerprint .= $this->getDeliveryAddress()->getCacheKey();
        }

        if ($this->getCurrency() instanceof Currency) {
            $fingerprint .= $this->getCurrency()->getCacheKey();
        }

        return $fingerprint;
    }

    /**
     * Format Price to locale.
     *
     * @param $price
     * @param Country $country
     * @param Currency $currency
     *
     * @return string
     */
    public function formatPrice($price, $country = null, $currency = null)
    {
        try {
            if(is_null($country)) {
                $country = static::getCountry();
            }

            if(is_null($currency)) {
                $currency = self::getCurrency();
            }

            $locale = \Zend_Locale::getLocaleToTerritory($country->getIsoCode());
            $zCurrency = new \Zend_Currency($locale);

            return $zCurrency->toCurrency($price, array('symbol' => $currency->getSymbol()));
        } catch (\Exception $ex) {
            echo $ex;
        }

        return $price;
    }

    /**
     * @return bool
     */
    public function displayPricesWithTax() {
        $session = $this->getSession();

        if(isset($session->displayPricesWithTax)) {
            return $session->displayPricesWithTax;
        }

        return true;
    }

    /**
     * Round Price.
     *
     * @param $price
     *
     * @return float
     */
    public function roundPrice($price)
    {
        return round($price, 2);
    }

    /**
     * Format Number without thousands seperator (eg 1540,32).
     *
     * @param $number float
     * @param $decimalPrecision int
     *
     * @return string
     */
    public function numberFormat($number, $decimalPrecision = 2)
    {
        return number_format($number, $decimalPrecision, ',', '');
    }

    /**
     * Determines if configured prices are gross or net prices
     *
     * @return bool
     */
    public function getPricesAreGross()
    {
        return Configuration::get("SYSTEM.BASE.PRICES.GROSS");
    }

    /**
     * Converts value from currency to currency.
     *
     * @param $value
     * @param Currency|null $toCurrency
     * @param Currency|null $fromCurrency
     *
     * @return mixed
     */
    public function convertToCurrency($value, $toCurrency = null, $fromCurrency = null)
    {
        if (!$fromCurrency instanceof Currency) {
            $fromCurrency = $this->getBaseCurrency();
        }

        if (!$toCurrency instanceof Currency) {
            $toCurrency = $this->getCurrency();
        }

        if ($fromCurrency instanceof Currency) {
            if ($toCurrency instanceof Currency && $toCurrency->getId() != $fromCurrency->getId()) {
                return $value * $toCurrency->getExchangeRate();
            }
        }

        return $value;
    }

    /**
     * get base Currency.
     *
     * @return Currency
     */
    public function getBaseCurrency()
    {
        $baseCurrency = Configuration::get('SYSTEM.BASE.CURRENCY');
        $currency = null;

        if ($baseCurrency) {
            $currency = Currency::getById($baseCurrency);
        }

        if (!$currency instanceof Currency) {
            Logger::warn('No SYSTEM.BASE.CURRENCY found, so EURO is going to be used! Please set your Default Currency in CoreShop Settings');

            $currency = Currency::getById(1); //TODO: Throw Exception because there is no base currency?
        }

        return $currency;
    }

    /**
     * Get CoreShop Session.
     *
     * @return Session
     */
    public function getSession()
    {
        if (php_sapi_name() === 'cli') {
            \Zend_Session::$_unitTestEnabled = true; //Force \Zend_Session with output before it has been started
        }

        return Session::get('CoreShop');
    }

    /**
     * get visitor
     *
     * @return Visitor|null
     */
    public function getVisitor() {
        $session = \CoreShop::getTools()->getSession();

        if (isset($session->visitorId)) {
            return Model\Visitor::getById($session->visitorId);
        }

        return null;
    }

    /**
     * Get current User.
     *
     * @return null|User
     */
    public function getUser()
    {
        if (\Pimcore::inDebugMode()) {
            if ($_REQUEST['coreshop_user']) {
                $user = User::getById($_REQUEST['coreshop_user']);

                return $user;
            }
        }

        $session = $this->getSession();

        if($session->user instanceof User) {
            return User::getById($session->user->getId());
        }

        return null;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $session = $this->getSession();

        $session->user = $user;
    }

    /**
     *
     */
    public function unsetUser()
    {
        $session = $this->getSession();

        unset($session->user);
        ;
    }

    /**
     * @return User\Address
     */
    public function getDeliveryAddress()
    {
        $cart = $this->prepareCart();
        $user = $this->getUser();

        if ($cart->getCustomerShippingAddress()) {
            return $cart->getCustomerShippingAddress();
        }

        if ($user instanceof User) {
            if (is_array($user->getAddresses()) && count($user->getAddresses()) > 0) {
                $addresses = $user->getAddresses();

                if($addresses[0] instanceof User\Address) {
                    return $addresses[0];
                }
            }
        }

        $address = User\Address::create();
        $address->setCountry($this->getCountry());

        return $address;
    }

    /**
     * Load Controller from CoreShop.
     *
     * @param string $controllerName
     */
    public function loadController($controllerName = '')
    {
        if (file_exists(CORESHOP_PATH . '/controllers/' . $controllerName . 'Controller.php')) {
            require CORESHOP_PATH . '/controllers/' . $controllerName . 'Controller.php';
        }
    }

    /**
     * Format Tax.
     *
     * TODO: Localization
     *
     * @param $tax
     *
     * @return string
     */
    public function formatTax($tax)
    {
        return $tax . '%';
    }

    /**
     * Prepare Cart.
     *
     * @param $resetCart bool create a new cart
     *
     * @return Cart
     */
    public function prepareCart($resetCart = false)
    {
        $cartSession = $this->getSession();

        $cart = null;

        if (!$resetCart) {
            if (isset($cartSession->cartId) && $cartSession->cartId !== 0) {
                $cart = Cart::getById($cartSession->cartId);
            } elseif (isset($cartSession->cartObj)) {
                if ($cartSession->cartObj instanceof Cart) {
                    $cart = $cartSession->cartObj;

                    if ($cart->getId() !== 0) {
                        unset($cartSession->cartObj);
                        $cartSession->cartId = $cart->getId();
                    }
                }
            }
        }

        if ($cart instanceof Cart) {
            //cart does already have a order, reset it!
            if( $cart->getOrder() instanceof Order) {
                //reset cartobj first
                $cartSession->cartObj = null;
                $cartSession->cartId = null;
                $cart = Cart::prepare();
                $cartSession->cartObj = $cart;
                return $cart;
            }

            if ($cart->getUser() === null && count($cart->getItems()) && $this->getUser() instanceof User) {
                $cart->setUser($this->getUser());
                $cart->save();
            }

            return $cart;
        }

        $cart = Cart::prepare();
        $cartSession->cartObj = $cart;
        $cartSession->cartId = null;

        return $cart;
    }

    /**
     * Get current Users Country.
     *
     * @return Country|null
     */
    public function getCountry()
    {
        $session = $this->getSession();
        $country = null;
        $cart = $this->prepareCart();

        if (\Pimcore::inDebugMode()) {
            if (!empty($_REQUEST["coreshop_country"])) {
                $country = Country::getById($_REQUEST["coreshop_country"]);

                if ($country instanceof Country) {
                    return $country;
                }
            }
        }

        if ($session->countryId) {
            $country = Country::getById($session->countryId);

            if ($country instanceof Country) {
                return $country;
            }
        }

        if ($cart instanceof Cart) {
            if (count($cart->getBillingAddress()) > 0) {
                $address = $cart->getBillingAddress();

                if ($address instanceof User\Address) {
                    $country = $address->getCountry();
                }
            }
        }

        if (!$country instanceof Country) {
            if ($this->getUser() instanceof User) {
                $user = $this->getUser();

                if (count($user->getAddresses()) > 0) {
                    $addresses = $user->getAddresses();
                    $country = $addresses[0]->getCountry();
                }
            }
        }

        if (!$country instanceof Country) {
            $geoDbFile = realpath(PIMCORE_WEBSITE_VAR.'/config/GeoLite2-City.mmdb');
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

                    if (!$this->checkIfIpIsPrivate($ip)) {
                        $record = $reader->city($ip);

                        $country = Country::getByIsoCode($record->country->isoCode);
                    }
                } catch (\Exception $e) {
                }
            }
        }

        if (!$country instanceof Country) {
            $country = \CoreShop::actionHook('country');

            if (!$country instanceof Country) {
                $country = Country::getById(Configuration::get('SYSTEM.BASE.COUNTRY'));

                if (!$country instanceof Country) {
                    Logger::warn('No SYSTEM.BASE.COUNTRY found, so AUSTRIA is going to be used! Please set your Default Country in CoreShop Settings');
                    $country = Country::getById(2);
                }
            }
        }

        $session->countryId = $country->getId();

        return $country;
    }

    /**
     * Check if ip is private.
     *
     * @param $ip
     *
     * @return bool
     */
    private function checkIfIpIsPrivate($ip)
    {
        $pri_addrs = array(
            '10.0.0.0|10.255.255.255', // single class A network
            '172.16.0.0|172.31.255.255', // 16 contiguous class B network
            '192.168.0.0|192.168.255.255', // 256 contiguous class C network
            '169.254.0.0|169.254.255.255', // Link-local address also refered to as Automatic Private IP Addressing
            '127.0.0.0|127.255.255.255', // localhost
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
     * Get current Currency by Country.
     *
     * @return Currency|null
     *
     * @throws \Exception
     */
    public function getCurrency()
    {
        $session = $this->getSession();

        if (\Pimcore::inDebugMode()) {
            if (!empty($_REQUEST["coreshop_currency"])) {
                $currency = Currency::getById($_REQUEST["coreshop_currency"]);

                if ($currency instanceof Currency) {
                    return $currency;
                }
            }
        }

        if ($session->currencyId) {
            $currency = Currency::getById($session->currencyId);

            if ($currency instanceof Currency) {
                return $currency;
            }
        }

        if ($this->getCountry()->getCurrency() instanceof Currency) {
            return $this->getCountry()->getCurrency();
        }

        return $this->getBaseCurrency();
    }

    /**
     * Validate VAT for address.
     *
     * @param $vatNumber string
     *
     * @return bool
     */
    public function validateVatNumber($vatNumber)
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

        for ($i = 0; $i < 3; ++$i) {
            if ($page_res = @file_get_contents($url)) {
                if (preg_match('/invalid VAT number/i', $page_res)) {
                    return false;
                } elseif (preg_match('/valid VAT number/i', $page_res)) {
                    return true;
                } else {
                    ++$i;
                }
            } else {
                sleep(1);
            }
        }

        return false;
    }

    /**
     * Retreive the values in an array.
     *
     * @param $object
     *
     * @return array
     */
    public function objectToArray(Object\Concrete $object)
    {
        return _objectToArray($object);
    }

    /**
     * get CoreShop Translate.
     *
     * @return \Zend_Translate_Adapter
     */
    public function getTranslate()
    {
        $lang = null;
        $user = \Pimcore\Tool\Admin::getCurrentUser();

        if ($user instanceof User) {
            $lang = $user->getLanguage();
        } else {
            $lang = $this->getLocale();
        }

        if (!$lang) {
            $lang = \Pimcore\Tool::getDefaultLanguage();
        }

        return Plugin::getTranslate($lang);
    }

    /**
     * Initializes the Template for the give Shop
     *
     * @param Shop $shop
     */
    public function initTemplateForShop(Shop $shop)
    {
        $template = $shop->getTemplate();

        if (!$template) {
            die("No template configured");
        }

        $templateBasePath = '';
        $templateResources = '';

        if (is_dir(PIMCORE_WEBSITE_PATH . '/views/scripts/coreshop/template/' . $template)) {
            $templateBasePath = PIMCORE_WEBSITE_PATH . "/views/scripts/coreshop/template";
            $templateResources = "/website/views/scripts/coreshop/template/" . $template . "/static/";
        }

        define("CORESHOP_TEMPLATE_BASE_PATH", $templateBasePath);
        define("CORESHOP_TEMPLATE_NAME", $template);
        define("CORESHOP_TEMPLATE_BASE", CORESHOP_TEMPLATE_BASE_PATH . "/base");
        define("CORESHOP_TEMPLATE_PATH", CORESHOP_TEMPLATE_BASE_PATH . "/" . $template);
        define("CORESHOP_TEMPLATE_RESOURCES", $templateResources);

        if (!is_dir(CORESHOP_TEMPLATE_PATH)) {
            Logger::critical(sprintf("Template with name '%s' not found. (%s)", $template, CORESHOP_TEMPLATE_PATH));
        }
    }

    /**
     * @return mixed|string
     */
    public function getLocale()
    {
        if (php_sapi_name() === 'cli') {
            \Zend_Registry::set("Zend_Locale", new \Zend_Locale());
        }

        if (\Zend_Registry::isRegistered("Zend_Locale")) {
            return \Zend_Registry::get("Zend_Locale");
        }

        return "en";
    }

    /**
     * @return mixed
     */
    public function getReferrer() {
        $referrer = parse_url($_SERVER['HTTP_REFERER']);
        $parsedHost = parse_url(Tool::getHostUrl());

        if (!isset($referrer['host']) || (!isset($referrer['path']) || !isset($referrer['path']))) {
            return false;
        }

        if($referrer['host'] === $parsedHost['host']) {
            return false;
        }

        return $_SERVER['HTTP_REFERER'];
    }
}
