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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SecurityServiceInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;
use Webmozart\Assert\Assert;

final class FrontendSecurityContext implements Context
{
    private $sharedStorage;
    private $securityService;
    private $customerRepository;
    private $customerFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $user = $this->customerRepository->findCustomerByEmail($email);
        Assert::notNull($user);

        $this->securityService->logIn($user);
    }

    /**
     * @Given I am a logged in customer
     */
    public function iAmLoggedInCustomer()
    {
        $customer = $this->customerFactory->createNew();
        $customer->setKey(File::getValidFilename('coreshop@pimcore.org'));
        $customer->setParent(Folder::getByPath('/'));
        $customer->setEmail('coreshop@pimcore.org');
        $customer->setFirstname(reset(explode('@', 'coreshop@pimcore.org')));
        $customer->setLastname(end(explode('@', 'coreshop@pimcore.org')));
        $customer->setPassword('coreshop');
        $customer->setPublished(true);
        $customer->save();

        $this->securityService->logIn($customer);

        $this->sharedStorage->set('customer', $customer);
    }
}
