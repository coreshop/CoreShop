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

namespace CoreShop\Model\Plugin;

use CoreShop\Model\Country;
use CoreShop\Model\TaxCalculator;
use CoreShop\Model\User\Address;

interface TaxManager
{
    /**
     * This method determine if the tax manager is available for the specified address.
     *
     * @param Address $address
     * @param string $type
     *
     * @return boolean
     */
    public static function isAvailableForThisAddress(Address $address, $type);

    /**
     * Return the tax calculator associated to this address
     *
     * @return TaxCalculator
     */
    public function getTaxCalculator();
}
