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

namespace CoreShop\Component\Core\Customer\Address;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Customer\CustomerTransformHelperInterface;
use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;

final class AddressAssignmentManager implements AddressAssignmentManagerInterface
{
    /**
     * @var CustomerTransformHelperInterface
     */
    protected $customerTransformHelper;

    /**
     * @param CustomerTransformHelperInterface $customerTransformHelper
     */
    public function __construct(CustomerTransformHelperInterface $customerTransformHelper)
    {
        $this->customerTransformHelper = $customerTransformHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getAddressAffiliationTypesForCustomer(CustomerInterface $customer)
    {
        if ($customer->getAddressAccessType() !== CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY) {
            return null;
        }

        return [
            'Own'     => CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN,
            'Company' => CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function detectAddressAffiliationForCustomer(CustomerInterface $customer, AddressInterface $address)
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

    /**
     * {@inheritDoc}
     */
    public function checkAddressAffiliationPermissionForCustomer(CustomerInterface $customer, AddressInterface $address)
    {
        if ($address->getId() === 0) {
            return true;
        }

        if ($customer->getAddressAccessType() === null) {
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
            return $customer->hasAddress($address) || $company instanceof CompanyInterface && $company->hasAddress($address);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function allocateAddressByAffiliation(CustomerInterface $customer, AddressInterface $address, ?string $affiliation)
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

        $address = $this->customerTransformHelper->moveAddressToNewAddressStack($address, $relationEntity);

        return $address;
    }
}
