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
