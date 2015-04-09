<?php

use CoreShop\Plugin;
use CoreShop\Config;
use CoreShop\Tool;

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
            "product" => array(
                "default-image" => $values["product.default-image"]
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
        $currency = new Zend_Currency($locale);
        $countries = $locale->getTranslationList('Territory', $language, 2);

        foreach($countries as $iso=>$name)
        {
            $countryLocale = Zend_Locale::getLocaleToTerritory($iso);
            $countryLocale = $countryLocale instanceof Zend_Locale ? $countryLocale : $locale;

            $currency->setLocale($countryLocale);

            $currenciesForCountry = $currency->getCurrencyList($iso);

            $currencyName = $currency->getName($currenciesForCountry[0], $countryLocale);
            $currencySymbol = $currency->getSymbol($currenciesForCountry[0], $countryLocale);

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
                $countryObject = new CoreShopCountry();
                $countryObject->setKey(\Pimcore\File::getValidFilename($iso));
                $countryObject->setCountry($iso);
                $countryObject->setActive(false);
                $countryObject->setParent(Tool::findOrCreateObjectFolder("/coreshop/countries"));
                $countryObject->setCurrency($currencyObject);
                $countryObject->setPublished(true);
                $countryObject->save();
            }
        }

        $this->_helper->json(array("success" => true));
    }
}
