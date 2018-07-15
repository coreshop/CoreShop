<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;

final class CustomerGroupContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $customerGroupFactory;

    /**
     * @var RepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $customerGroupFactory
     * @param RepositoryInterface $customerGroupRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $customerGroupFactory,
        RepositoryInterface $customerGroupRepository,
        ObjectManager $objectManager
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->customerGroupFactory = $customerGroupFactory;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given /^the site has a customer-group "([^"]+)"$/
     */
    public function theSiteHasACustomerGroup(string $name)
    {
        $group = $this->createCustomerGroup($name);

        $this->saveCustomerGroup($group);
    }

    /**
     * @param string $name
     * @return CustomerGroupInterface
     */
    private function createCustomerGroup(string $name)
    {
        /** @var CustomerGroupInterface $group*/
        $group = $this->customerGroupFactory->createNew();

        $group->setKey(File::getValidFilename($name));
        $group->setParent(Folder::getByPath('/'));
        $group->setName($name);

        return $group;
    }

    /**
     * @param CustomerGroupInterface $group
     */
    private function saveCustomerGroup(CustomerGroupInterface $group)
    {
        $this->objectManager->persist($group);
        $this->objectManager->flush();

        $this->sharedStorage->set('customer_group', $group);
    }
}
