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
use Webmozart\Assert\Assert;

final class FrontendSecurityContext implements Context
{
    private $sharedStorage;
    private $securityService;
    private $customerRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->customerRepository = $customerRepository;
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
}
