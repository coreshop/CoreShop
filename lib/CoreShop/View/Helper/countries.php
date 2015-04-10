<?php

namespace CoreShop\View\Helper;

use Pimcore\Model\Object\CoreShopCountry;

class Countries
{
    public $view;

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function countries()
    {
        $countries = \CoreShop\Country::getActiveCountries();

        return $countries;
    }
}
