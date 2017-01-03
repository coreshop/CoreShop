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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\View\Helper;

use CoreShop\Model\Country;

/**
 * Class Countries
 * @package CoreShop\View\Helper
 */
class Countries
{
    public $view;

    /**
     * Set View.
     *
     * @param \Zend_View_Interface $view
     */
    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    /**
     * get active countries.
     *
     * @return array
     */
    public function countries()
    {
        $countries = Country::getActiveCountries();

        return $countries;
    }
}
