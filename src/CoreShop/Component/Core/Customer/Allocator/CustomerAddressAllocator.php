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
use CoreShop\Component\Customer\Model\CustomerInterface;

final class CustomerAddressAllocator implements CustomerAddressAllocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function allocateForCustomer(CustomerInterface $customer)
    {
        $addressAccessType = $customer->getAddressAccessType();

        if (is_null($addressAccessType) || $addressAccessType === self::ADDRESS_ACCESS_TYPE_OWN_ONLY) {
            return $customer->getAddresses();
        }

        if (!$customer->getCompany() instanceof CompanyInterface) {
            return $customer->getAddresses();
        }

        if ($addressAccessType === self::ADDRESS_ACCESS_TYPE_COMPANY_ONLY) {
            return $customer->getCompany()->getAddresses();
        }

        if ($addressAccessType === self::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY) {
            return array_merge($customer->getAddresses(), $customer->getCompany()->getAddresses());
        }

        // @todo: throw exception?

        return [];
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
