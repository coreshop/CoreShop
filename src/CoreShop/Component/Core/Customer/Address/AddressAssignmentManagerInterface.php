<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Customer\Address;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

interface AddressAssignmentManagerInterface
{
    /**
     * @param CustomerInterface $customer
     * @param bool              $useTranslationKeys
     *
     * @return array|null
     */
    public function getAddressAffiliationTypesForCustomer(CustomerInterface $customer, bool $useTranslationKeys = true);

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     *
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function detectAddressAffiliationForCustomer(CustomerInterface $customer, AddressInterface $address);

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     *
     * @return bool
     */
    public function checkAddressAffiliationPermissionForCustomer(CustomerInterface $customer, AddressInterface $address);

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     * @param string|null       $affiliation
     *
     * @return AddressInterface
     * @throws \InvalidArgumentException
     */
    public function allocateAddressByAffiliation(CustomerInterface $customer, AddressInterface $address, ?string $affiliation);
}
