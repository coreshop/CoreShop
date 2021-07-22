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
use CoreShop\Component\Core\Customer\CustomerTransformHelperInterface;
use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

final class AddressAssignmentManager implements AddressAssignmentManagerInterface
{
    protected CustomerTransformHelperInterface $customerTransformHelper;

    public function __construct(CustomerTransformHelperInterface $customerTransformHelper)
    {
        $this->customerTransformHelper = $customerTransformHelper;
    }

    public function getAddressAffiliationTypesForCustomer(CustomerInterface $customer, bool $useTranslationKeys = true): ?array
    {
        if ($customer->getAddressAccessType() !== CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY) {
            return null;
        }

        $ownKey = $useTranslationKeys ? 'coreshop.form.customer.address_affiliation.own' : 'Own';
        $companyKey = $useTranslationKeys ? 'coreshop.form.customer.address_affiliation.company' : 'Company';

        return [
            $ownKey     => CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN,
            $companyKey => CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY,
        ];
    }

    public function detectAddressAffiliationForCustomer(CustomerInterface $customer, AddressInterface $address): ?string
    {
        if ($address->getId() === 0) {
            return null;
        }

        $company = $customer->getCompany();
        if (!$company instanceof CompanyInterface) {
            return CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN;
        }

        if ($customer->hasAddress($address)) {
            return CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN;
        } elseif ($company->hasAddress($address)) {
            return CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY;
        }

        throw new \InvalidArgumentException(sprintf('Could not determine address affiliation for customer "%s"', $customer->getId()));

    }

    public function checkAddressAffiliationPermissionForCustomer(CustomerInterface $customer, AddressInterface $address): bool
    {
        if ($address->getId() === 0) {
            return true;
        }

        if (empty($customer->getAddressAccessType())) {
            return $customer->hasAddress($address);
        }

        if ($customer->getAddressAccessType() === CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY) {
            return $customer->hasAddress($address);
        }

        $company = $customer->getCompany();
        if ($customer->getAddressAccessType() === CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_COMPANY_ONLY) {
            return $company instanceof CompanyInterface && $company->hasAddress($address);
        }

        if ($customer->getAddressAccessType() === CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY) {
            return $customer->hasAddress($address) || ($company instanceof CompanyInterface && $company->hasAddress($address));
        }

        return false;
    }

    public function allocateAddressByAffiliation(CustomerInterface $customer, AddressInterface $address, ?string $affiliation): AddressInterface
    {
        $company = $customer->getCompany();

        if (!$company instanceof CompanyInterface || $affiliation === null) {
            $affiliation = CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN;
        }

        $relationEntity = null;
        if ($affiliation === CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN) {
            $relationEntity = $customer;
        } elseif ($affiliation === CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY) {
            $relationEntity = $company;
        }

        if ($relationEntity === null) {
            throw new \InvalidArgumentException(
                sprintf('Could not determine address path for customer with id %d with affiliation "%s"', $customer->getId(), $affiliation)
            );
        }

        //If it's a customer address, and the customer doesn't have one yet, use this address as default and allow it for all types
        if ($affiliation === CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN && 0 === count($customer->getAddresses())) {
            $address->setAddressIdentifier(null);
            $customer->setDefaultAddress($address);
        }

        return $this->customerTransformHelper->moveAddressToNewAddressStack($address, $relationEntity);
    }
}
