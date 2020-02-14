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

namespace CoreShop\Component\Core\Customer;

use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerTransformHelper implements CustomerTransformHelperInterface
{
    /**
     * @var FactoryInterface
     */
    protected $companyFactory;

    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var string
     */
    protected $companyFolder;

    /**
     * @var string
     */
    protected $addressFolder;

    /**
     * @param FactoryInterface       $companyFactory
     * @param ObjectServiceInterface $objectService
     * @param string                 $companyFolder
     * @param string                 $addressFolder
     */
    public function __construct(
        FactoryInterface $companyFactory,
        ObjectServiceInterface $objectService,
        $companyFolder,
        $addressFolder
    ) {
        $this->companyFactory = $companyFactory;
        $this->objectService = $objectService;
        $this->companyFolder = $companyFolder;
        $this->addressFolder = $addressFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAddressFolderPath(string $rootPath)
    {
        return $this->objectService->createFolderByPath(sprintf('%s/%s', $rootPath, $this->addressFolder));
    }

    /**
     * {@inheritdoc}
     */
    public function getSaveKeyForMoving(ElementInterface $object, ElementInterface $newParent)
    {
        $incrementId = 1;
        $originalKey = $object->getKey();
        $newKey = $object->getKey();
        $newPath = sprintf('%s/%s', $newParent->getFullPath(), $originalKey);

        while (Service::pathExists($newPath, 'object')) {
            $newKey = sprintf('%s-%d', $originalKey, $incrementId);
            $newPath = sprintf('%s/%s', $newParent->getFullPath(), $newKey);
            $incrementId++;
        }

        return $newKey;
    }

    /**
     * {@inheritdoc}
     */
    public function moveCustomerToNewCompany(CustomerInterface $customer, array $transformOptions)
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
        $company->setParent($this->objectService->createFolderByPath(sprintf(
            '/%s/%s',
            $this->companyFolder, mb_strtoupper(mb_substr($customer->getLastname(), 0, 1))
        )));

        $company->setKey(File::getValidFilename($company->getName()));
        $company->setKey(\Pimcore\Model\DataObject\Service::getUniqueKey($company));

        if ($company instanceof AbstractObject) {
            $company->setChildrenSortBy('index');
        }

        try {
            $company->save();
        } catch (\Throwable $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->moveCustomerToCompany($customer, $company, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function moveCustomerToExistingCompany(CustomerInterface $customer, CompanyInterface $company, array $transformOptions)
    {
        $resolver = $this->getMoveOptionsResolver();

        try {
            $options = $resolver->resolve($transformOptions);
        } catch (\Throwable $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->moveCustomerToCompany($customer, $company, $options);
    }

    /**
     * @param CustomerInterface $customer
     * @param CompanyInterface  $company
     * @param array             $options
     *
     * @throws \Exception
     */
    protected function moveCustomerToCompany(CustomerInterface $customer, CompanyInterface $company, array $options)
    {
        $customer->setParent($company);
        $customer->setKey($this->getSaveKeyForMoving($customer, $company));
        $customer->setCompany($company);
        $customer->setAddressAccessType($options['addressAccessType']);

        if ($options['addressAssignmentType'] === 'move') {

            $newPath = sprintf('/%s/%s', $company->getFullPath(), $this->addressFolder);
            $parent = $this->objectService->createFolderByPath($newPath);

            foreach ($customer->getAddresses() as $address) {

                $address->setParent($parent);
                $address->setKey($this->getSaveKeyForMoving($address, $parent));
                $address->save();

                $customer->removeAddress($address);
                $company->addAddress($address);
            }

            if ($company instanceof Concrete) {
                $company->setOmitMandatoryCheck(true);
            }

            $company->save();
        }

        if ($customer instanceof Concrete) {
            $customer->setOmitMandatoryCheck(true);
        }

        $customer->save();
    }

    /**
     * @return OptionsResolver
     */
    protected function getMoveOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'addressAssignmentType' => 'move',
            'addressAccessType'     => 'own_only'
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

}
