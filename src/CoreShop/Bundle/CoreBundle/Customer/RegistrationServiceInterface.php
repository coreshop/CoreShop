<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

/**
 * @deprecated This class is deprecated since 2.2.0 in favor of CustomerManagerInterface and will be removed with 3.0.0. Please don't use anymore.
 */
interface RegistrationServiceInterface
{
    /**
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     * @param array             $formData
     * @param bool              $isGuest
     *
     * @return mixed
     */
    public function registerCustomer(CustomerInterface $customer, AddressInterface $address, $formData, $isGuest = false);
}
