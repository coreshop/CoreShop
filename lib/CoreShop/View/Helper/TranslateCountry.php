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

namespace CoreShop\View\Helper;

use CoreShop\Model\Country;

/**
 * Class TranslateCountry
 * @package CoreShop\View\Helper
 */
class TranslateCountry
{
    public $view;

    /**
     * set view.
     *
     * @param \Zend_View_Interface $view
     */
    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    /**
     * translate country.
     *
     * @param $countryCode
     *
     * @return bool
     *
     * @throws \Zend_Exception
     */
    public function translateCountry($countryCode)
    {
        if ($countryCode instanceof Country) {
            $countryCode = $countryCode->getIsoCode();
        }

        $locale = \Zend_Registry::get('Zend_Locale');
        $countries = $locale->getTranslationList('Territory', $locale, 2);

        if (array_key_exists($countryCode, $countries)) {
            return $countries[$countryCode];
        }

        return false;
    }
}
