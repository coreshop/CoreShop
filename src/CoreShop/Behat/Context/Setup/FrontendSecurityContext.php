<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SecurityServiceInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\User\Repository\UserRepositoryInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;
use Webmozart\Assert\Assert;

final class FrontendSecurityContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private SecurityServiceInterface $securityService;
    private UserRepositoryInterface $userRepository;
    private FactoryInterface $customerFactory;
    private FactoryInterface $userFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        UserRepositoryInterface $userRepository,
        FactoryInterface $customerFactory,
        FactoryInterface $userFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->userRepository = $userRepository;
        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $user = $this->userRepository->findByLoginIdentifier($email);
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
        $customer->setPublished(true);
        $customer->setEmail('coreshop@pimcore.org');
        $customer->setFirstname('coreshop');
        $customer->setLastname('pimcore');
//        $customer->setPassword('coreshop');
        $customer->save();

        /**
         * @var UserInterface $user
         */
        $user = $this->userFactory->createNew();
        $user->setPassword('coreshop');
        $user->setLoginIdentifier('coreshop@pimcore.org');
        $user->setKey(File::getValidFilename('coreshop@pimcore.org-user'));
        $user->setParent(Folder::getByPath('/'));
        $user->setPublished(true);
        $user->setCustomer($customer);
        $user->save();

        $customer->setUser($user);
        $customer->save();

        $this->securityService->logIn($user);

        $this->sharedStorage->set('customer', $customer);
        $this->sharedStorage->set('user', $user);
    }
}
