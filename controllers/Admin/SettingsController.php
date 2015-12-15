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

use CoreShop\Plugin;
use CoreShop\Config;
use CoreShop\Tool;
use CoreShop\Helper\Country;

use CoreShop\Model;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_SettingsController extends Admin
{
    public function installedAction()
    {
        $values = Config::getConfig();

        $valueArray = $values->toArray();

        $this->_helper->json(array("isInstalled" => $valueArray['isInstalled'] == "1" ? true : false));
    }

    public function getAction()
    {
        $values = Config::getConfig();

        $valueArray = $values->toArray();

        $response = array(
            "values" => $valueArray,
        );

        $this->_helper->json($response);
        $this->_helper->json(false);
    }

    public function setAction()
    {
        $values = \Zend_Json::decode($this->getParam("data"));

        // convert all special characters to their entities so the xml writer can put it into the file
        $values = array_htmlspecialchars($values);

        // email settings
        $oldConfig = Config::getConfig();
        $settings = $oldConfig->toArray();

        $settings['base']['base-currency'] = $values["base.base-currency"];
        $settings['product']['default-image'] = $values["product.default-image"];
        $settings['product']['days-as-new'] = $values["product.days-as-new"];
        $settings['category']['default-image'] = $values["category.default-image"];
        $settings['template']['name'] = $values['template.name'];

        $config = new \Zend_Config($settings, true);
        $writer = new \Zend_Config_Writer_Xml(array(
            "config" => $config,
            "filename" => CORESHOP_CONFIGURATION
        ));
        $writer->write();

        $this->_helper->json(array("success" => true));
    }

    public function createcountriesAction()
    {
        $language = $this->_getParam("language");

        $locale = new Zend_Locale($language);
        $regions = Zend_Locale::getTranslationList('RegionToTerritory');

        $countryGroup = array();

        foreach ($regions as $region => $countriesString) {
            $countries = explode(' ', $countriesString);

            foreach($countries as $country) {
                $countryGroup[$country] = $locale->getTranslation($region, 'territory', $locale);
            }

        }

        $countries = Country::getData();

        foreach($countries as $iso=>$name)
        {
            $currencyCode = Country::getCurrencyCodeForCountry($iso);
            $currencyDetail = Country::getCurrencyDetail($currencyCode);

            if(!$currencyCode || !$currencyDetail) {

                continue;
            }

            $currencyName = $currencyDetail['name'];
            $currencySymbol = $currencyDetail['symbol'];
            $currencyIsoNumber = $currencyDetail['isocode'];

            //Check if currency Object already exists
            $currencyObject = Model\Currency::getByName($currencyName);

            if(!$currencyObject instanceof Model\Currency)
            {
                $currencyObject = new Model\Currency();
                $currencyObject->setSymbol($currencySymbol);
                $currencyObject->setNumericIsoCode($currencyIsoNumber);
                $currencyObject->setIsoCode($currencyCode);
                $currencyObject->setExchangeRate(1);

            }

            $currencyObject->setName($currencyName);
            $currencyObject->save();

            //Check if country Object already exists
            $countryObject = Model\Country::getByIsoCode($iso);

            if(!$countryObject instanceof Model\Country)
            {
                $countryObject = new Model\Country();
            }

            $countryObject->setName($name);
            $countryObject->setIsoCode($iso);
            $countryObject->setActive(false);
            $countryObject->setCurrency($currencyObject);
            $countryObject->save();
        }

        $this->_helper->json(array("success" => true));
    }
}
