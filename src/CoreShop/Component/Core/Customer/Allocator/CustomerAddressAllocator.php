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

declare(strict_types=1);

namespace CoreShop\Component\Core\Customer\Allocator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

final class CustomerAddressAllocator implements CustomerAddressAllocatorInterface
{
    public function allocateForCustomer(CustomerInterface $customer): array
    {
        $addressAccessType = $customer->getAddressAccessType();

        if (null === $addressAccessType || self::ADDRESS_ACCESS_TYPE_OWN_ONLY === $addressAccessType) {
            return $customer->getAddresses();
        }

        $company = $customer->getCompany();

        if (!$company instanceof CompanyInterface) {
            return $customer->getAddresses();
        }

        if (self::ADDRESS_ACCESS_TYPE_COMPANY_ONLY === $addressAccessType) {
            return $company->getAddresses();
        }

        if (self::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY === $addressAccessType) {
            return array_merge($customer->getAddresses(), $company->getAddresses());
        }

        throw new \Exception(sprintf('Cannot allocate addresses for customer %d with access type "%s"', $customer->getId(), $addressAccessType));
    }

    public function isOwnerOfAddress(CustomerInterface $customer, AddressInterface $address): bool
    {
        return $customer->hasAddress($address);
    }
}
