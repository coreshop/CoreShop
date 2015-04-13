<?php

use CoreShop\Plugin;
use CoreShop\Config;
use CoreShop\Tool;
use CoreShop\Helper\Country;

use Pimcore\Model\Object\CoreShopCurrency;
use Pimcore\Model\Object\CoreShopCountry;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_SettingsController extends Admin
{
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
        $oldValues = $oldConfig->toArray();

        $settings = array(
            "base" => array(
                "base-currency" => $values["base.base-currency"]
            ),
            "product" => array(
                "default-image" => $values["product.default-image"],
                "days-as-new" => $values["product.days-as-new"]
            ),
            "category" => array(
                "default-image" => $values["category.default-image"]
            )
        );

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

        $countries = Country::getCountries();

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
            $list = CoreShopCurrency::getByName($currencyName);
            $currencyObject = null;

            if(count($list->getObjects()) > 0) {
                $currencyObject = $list->current();
            }

            if(!$currencyObject instanceof CoreShopCurrency)
            {
                $currencyObject = new CoreShopCurrency();
                $currencyObject->setKey(\Pimcore\File::getValidFilename($currencyName));
                $currencyObject->setSymbol($currencySymbol);
                $currencyObject->setIsoNumber($currencyIsoNumber);
                $currencyObject->setIsoCode($currencyCode);
                $currencyObject->setExchangeRate(1);
                $currencyObject->setParent(Tool::findOrCreateObjectFolder("/coreshop/currencies"));
                $currencyObject->setPublished(true);

            }

            $currencyObject->setName($currencyName, $language);
            $currencyObject->save();

            //Check if country Object already exists
            $countryList = CoreShopCountry::getByCountry($iso);
            $countryObject = null;

            if(count($countryList->getObjects()) > 0) {
                $countryObject = $countryList->current();
            }

            if(!$countryObject instanceof CoreShopCountry)
            {
                $folderPath = "/coreshop/countries";

                if(array_key_exists($iso, $countryGroup))
                    $folderPath .= "/" . \Pimcore\File::getValidFilename($countryGroup[$iso]);

                $countryObject = new CoreShopCountry();
                $countryObject->setKey(\Pimcore\File::getValidFilename($name));
                $countryObject->setCountry($iso);
                $countryObject->setActive(false);
                $countryObject->setParent(Tool::findOrCreateObjectFolder($folderPath));
                $countryObject->setCurrency($currencyObject);
                $countryObject->setPublished(true);
                $countryObject->save();
            }
        }

        $this->_helper->json(array("success" => true));
    }
}
