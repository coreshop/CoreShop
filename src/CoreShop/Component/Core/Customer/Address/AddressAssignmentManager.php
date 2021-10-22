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

namespace CoreShop\Component\Core\Customer\Address;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Customer\CustomerTransformHelperInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

final class AddressAssignmentManager implements AddressAssignmentManagerInterface
{
    public function __construct(protected CustomerTransformHelperInterface $customerTransformHelper)
    {
    }

    public function getAddressAffiliationTypesForCustomer(CustomerInterface $customer, bool $useTranslationKeys = true): ?array
    {
        if (CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY !== $customer->getAddressAccessType()) {
            return null;
        }

        $ownKey = $useTranslationKeys ? 'coreshop.form.customer.address_affiliation.own' : 'Own';
        $companyKey = $useTranslationKeys ? 'coreshop.form.customer.address_affiliation.company' : 'Company';

        return [
            $ownKey => CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN,
            $companyKey => CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY,
        ];
    }

    public function detectAddressAffiliationForCustomer(CustomerInterface $customer, AddressInterface $address): ?string
    {
        if (0 === $address->getId()) {
            return null;
        }

        $company = $customer->getCompany();
        if (!$company instanceof CompanyInterface) {
            return CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN;
        }

        if ($customer->hasAddress($address)) {
            return CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN;
        }
        if ($company->hasAddress($address)) {
            return CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY;
        }

        throw new \InvalidArgumentException(sprintf('Could not determine address affiliation for customer "%s"', $customer->getId()));
    }

    public function checkAddressAffiliationPermissionForCustomer(CustomerInterface $customer, AddressInterface $address): bool
    {
        if (0 === $address->getId()) {
            return true;
        }

        if (empty($customer->getAddressAccessType())) {
            return $customer->hasAddress($address);
        }

        if (CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY === $customer->getAddressAccessType()) {
            return $customer->hasAddress($address);
        }

        $company = $customer->getCompany();
        if (CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_COMPANY_ONLY === $customer->getAddressAccessType()) {
            return $company instanceof CompanyInterface && $company->hasAddress($address);
        }

        if (CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY === $customer->getAddressAccessType()) {
            return $customer->hasAddress($address) || ($company instanceof CompanyInterface && $company->hasAddress($address));
        }

        return false;
    }

    public function allocateAddressByAffiliation(CustomerInterface $customer, AddressInterface $address, ?string $affiliation): AddressInterface
    {
        $company = $customer->getCompany();

        if (!$company instanceof CompanyInterface || null === $affiliation) {
            $affiliation = CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN;
        }

        $relationEntity = null;
        if (CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN === $affiliation) {
            $relationEntity = $customer;
        } elseif (CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY === $affiliation) {
            $relationEntity = $company;
        }

        if (null === $relationEntity) {
            throw new \InvalidArgumentException(sprintf('Could not determine address path for customer with id %d with affiliation "%s"', $customer->getId(), $affiliation));
        }

        //If it's a customer address, and the customer doesn't have one yet, use this address as default and allow it for all types
        if (CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN === $affiliation && 0 === count($customer->getAddresses())) {
            $address->setAddressIdentifier(null);
            $customer->setDefaultAddress($address);
        }

        return $this->customerTransformHelper->moveAddressToNewAddressStack($address, $relationEntity);
    }
}
