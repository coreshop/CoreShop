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

use CoreShop\Component\Address\Model\AddressesAwareInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Customer\CustomerTransformHelperInterface;
use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Pimcore\Model\DataObject;
use Pimcore\Model\Dependency;

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

        $customerPath = null;
        if ($affiliation === CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN) {
            $customerPath = $customer->getFullPath();
        } elseif ($affiliation === CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY) {
            $customerPath = $customer->getCompany()->getFullPath();
        }

        if ($customerPath === null) {
            throw new \InvalidArgumentException(
                sprintf('Could not determine address path for customer with id %d with affiliation "%s"', $customer->getId(), $affiliation)
            );
        }

        $parent = $this->customerTransformHelper->getEntityAddressFolderPath($customerPath);

        // no affiliation has changed, return
        if ($this->isNewAddress($address) === false && $address->getParent()->getId() === $parent->getId()) {
            return $address;
        }

        // set new or changed parent
        $address->setKey($this->customerTransformHelper->getSaveKeyForMoving($address, $parent));
        $address->setParent($parent);

        // remove old relations
        $this->removeAddressRelations($customer, $address);

        // save address first before adding it as relation
        if ($this->isNewAddress($address)) {
            $this->saveAddressWithoutVersioning($address);
        }

        if ($affiliation === CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_OWN) {
            $customer->addAddress($address);
            $customer->save();
        }

        if ($affiliation === CustomerAddressAllocatorInterface::ADDRESS_AFFILIATION_TYPE_COMPANY) {
            $company = $customer->getCompany();
            if ($company instanceof CompanyInterface) {
                $company->addAddress($address);
                $company->save();
            }
        }

        return $address;
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     */
    protected function removeAddressRelations(CustomerInterface $customer, AddressInterface $address)
    {
        // no need to search for dependencies: address is new.
        if ($this->isNewAddress($address)) {
            return;
        }

        $dependenciesObjects = [];
        $dependenciesResult = Dependency::getBySourceId($address->getId(), 'object');

        foreach ($dependenciesResult->getRequiredBy() as $r) {
            if ($r['type'] === 'object') {
                $object = DataObject::getById($r['id']);
                if ($object instanceof AddressesAwareInterface) {
                    $dependenciesObjects[] = $object;
                }
            }
        }

        /** @var AddressesAwareInterface $dependenciesObject */
        foreach ($dependenciesObjects as $dependenciesObject) {
            if ($dependenciesObject->hasAddress($address)) {
                $dependenciesObject->removeAddress($address);
                $dependenciesObject->save();
            }
        }
    }

    /**
     * @param AddressInterface $address
     */
    protected function saveAddressWithoutVersioning(AddressInterface $address)
    {
        VersionHelper::useVersioning(function () use ($address) {
            $address->save();
        }, false);
    }

    /**
     * @param AddressInterface $address
     *
     * @return bool
     */
    protected function isNewAddress(AddressInterface $address)
    {
        return is_null($address->getId()) || $address->getId() === 0;
    }
}
