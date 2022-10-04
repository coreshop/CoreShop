<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Customer;

use CoreShop\Component\Address\Model\AddressesAwareInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Pimcore\File;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Dependency;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerTransformHelper implements CustomerTransformHelperInterface
{
    public function __construct(
        protected FactoryInterface $companyFactory,
        protected FolderCreationServiceInterface $folderCreationService,
    ) {
    }

    public function getEntityAddressFolderPath(AddressInterface $address, string $rootPath): DataObject\Folder
    {
        return $this->folderCreationService->createFolderForResource(
            $address,
            ['prefix' => $rootPath],
        );
    }

    public function getSaveKeyForMoving(ElementInterface $object, ElementInterface $newParent): string
    {
        $incrementId = 1;
        $originalKey = $object->getKey();
        $newKey = $object->getKey();
        $newPath = sprintf('%s/%s', $newParent->getFullPath(), $originalKey);

        while (Service::pathExists($newPath, 'object')) {
            $newKey = sprintf('%s-%d', $originalKey, $incrementId);
            $newPath = sprintf('%s/%s', $newParent->getFullPath(), $newKey);
            ++$incrementId;
        }

        return $newKey;
    }

    public function moveCustomerToNewCompany(CustomerInterface $customer, array $transformOptions): CustomerInterface
    {
        $resolver = $this->getMoveOptionsResolver();

        $resolver->setDefault('companyData', []);
        $resolver->setAllowedTypes('companyData', 'array');

        try {
            $options = $resolver->resolve($transformOptions);
        } catch (\Throwable $e) {
            throw new ValidationException($e->getMessage());
        }

        /** @var CompanyInterface $company */
        $company = $this->companyFactory->createNew();

        $company->setValues($options['companyData']);
        $company->setPublished(true);
        $company->setParent(
            $this->folderCreationService->createFolderForResource(
                $company,
                ['suffix' => mb_strtoupper(mb_substr($options['companyData']['name'] ?? '--', 0, 1))],
            ),
        );

        /** @psalm-suppress InternalMethod */
        $company->setKey(File::getValidFilename($company->getName()));

        if ($company instanceof Concrete) {
            $company->setKey(DataObject\Service::getUniqueKey($company));
            $company->setChildrenSortBy('index');
        }

        try {
            $this->forceSave($company);
        } catch (\Throwable $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->moveCustomerToCompany($customer, $company, $options);
    }

    public function moveCustomerToExistingCompany(CustomerInterface $customer, CompanyInterface $company, array $transformOptions): CustomerInterface
    {
        $resolver = $this->getMoveOptionsResolver();

        try {
            $options = $resolver->resolve($transformOptions);
        } catch (\Throwable $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->moveCustomerToCompany($customer, $company, $options);
    }

    public function moveAddressToNewAddressStack(AddressInterface $address, ElementInterface $newHolder, bool $removeOldRelations = true): AddressInterface
    {
        $path = $newHolder->getFullPath();
        $newParent = $this->getEntityAddressFolderPath($address, $path);

        if (!$newHolder instanceof AddressesAwareInterface) {
            return $address;
        }

        // set new or changed parent
        if ($this->isNewEntity($address) === true || $address->getParent()?->getId() !== $newParent->getId()) {
            $address->setParent($newParent);
            $address->setKey($this->getSaveKeyForMoving($address, $newParent));

            // remove old relations
            if ($removeOldRelations === true) {
                $this->removeAddressRelations($address);
            }
        }

        $this->forceSave($address, false);

        $newHolder->addAddress($address);

        $this->forceSave($newHolder);

        return $address;
    }

    private function moveCustomerToCompany(CustomerInterface $customer, CompanyInterface $company, array $options): CustomerInterface
    {
        $customer->setParent($company);
        $customer->setKey($this->getSaveKeyForMoving($customer, $company));
        $customer->setCompany($company);
        $customer->setAddressAccessType($options['addressAccessType']);

        // @todo: fire pre event

        if ($options['addressAssignmentType'] === 'move') {
            foreach ($customer->getAddresses() as $address) {
                $this->moveAddressToNewAddressStack($address, $company);
            }
        }

        $this->forceSave($customer);

        return $customer;
    }

    private function removeAddressRelations(AddressInterface $address): void
    {
        // no need to search for dependencies: address is new.
        if ($this->isNewEntity($address)) {
            return;
        }

        $dependenciesObjects = [];
        /** @psalm-suppress InternalClass,InternalMethod */
        $dependenciesResult = Dependency::getBySourceId((int) $address->getId(), 'object');

        /** @psalm-suppress InternalMethod */
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
            $save = false;
            if ($dependenciesObject->hasAddress($address)) {
                $save = true;
                $dependenciesObject->removeAddress($address);
            }

            if ($dependenciesObject instanceof CustomerInterface) {
                if ($dependenciesObject->getDefaultAddress() instanceof AddressInterface) {
                    if ($dependenciesObject->getDefaultAddress()->getId() === $address->getId()) {
                        $save = true;
                        $dependenciesObject->setDefaultAddress(null);
                    }
                }
            }

            if ($save === true) {
                $this->forceSave($dependenciesObject);
            }
        }
    }

    private function getMoveOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'addressAssignmentType' => 'move',
            'addressAccessType' => 'own_only',
        ]);

        $resolver->setAllowedTypes('addressAssignmentType', 'string');
        $resolver->setAllowedTypes('addressAccessType', 'string');

        $resolver->setAllowedValues('addressAssignmentType', ['move', 'keep']);
        $resolver->setAllowedValues('addressAccessType', [
            CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY,
            CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_COMPANY_ONLY,
            CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY,
        ]);

        return $resolver;
    }

    /**
     * @param AddressInterface|AddressesAwareInterface|ElementInterface&AddressesAwareInterface $element
     */
    private function forceSave(ElementInterface&AddressesAwareInterface|AddressInterface|AddressesAwareInterfaced $element, bool $useVersioning = true): void
    {
        if ($element instanceof Concrete) {
            $element->setOmitMandatoryCheck(true);
        }

        if ($element instanceof ElementInterface) {
            VersionHelper::useVersioning(function () use ($element) {
                $element->save();
            }, $useVersioning);
        }
    }

    private function isNewEntity(ElementInterface $element): bool
    {
        return null === $element->getId() || $element->getId() === 0;
    }
}
