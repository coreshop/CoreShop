<?php

namespace CoreShop\View\Helper;

use Pimcore\Model\Object\CoreShopCountry;

class TranslateCountry
{
    public $view;

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function translateCountry($countryCode)
    {
        if($countryCode instanceof CoreShopCountry)
            $countryCode = $countryCode->getCountry();

        $locale = \Zend_Registry::get("Zend_Locale");
        $countries = $locale->getTranslationList('Territory', $locale, 2);

        if(array_key_exists($countryCode, $countries))
            return $countries[$countryCode];

        return false;
    }
}
