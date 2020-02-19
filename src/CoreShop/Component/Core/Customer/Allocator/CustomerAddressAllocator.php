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

namespace CoreShop\Component\Core\Customer\Allocator;

use CoreShop\Component\Address\Model\AddressesAwareInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

final class CustomerAddressAllocator implements CustomerAddressAllocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function allocateForCustomer(CustomerInterface $customer)
    {
        $addressAccessType = $customer->getAddressAccessType();

        if ($addressAccessType === null || $addressAccessType === self::ADDRESS_ACCESS_TYPE_OWN_ONLY) {
            return $customer->getAddresses();
        }

        $company = $customer->getCompany();

        if (!$company instanceof CompanyInterface) {
            return $customer->getAddresses();
        }

        if ($addressAccessType === self::ADDRESS_ACCESS_TYPE_COMPANY_ONLY) {
            return $company->getAddresses();
        }

        if ($addressAccessType === self::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY) {
            return array_merge($customer->getAddresses(), $company->getAddresses());
        }

        throw new \Exception(sprintf('Cannot allocate addresses for customer %d with access type "%s"', $customer->getId(), $addressAccessType));
    }

    /**
     * {@inheritdoc}
     */
    public function isOwnerOfAddress(CustomerInterface $customer, AddressInterface $address)
    {
        if (!$customer instanceof AddressesAwareInterface) {
            return false;
        }

        return $customer->hasAddress($address);
    }
}
