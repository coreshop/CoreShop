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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Webmozart\Assert\Assert;

final class CustomerContext implements Context
{
    private $sharedStorage;
    private $customerRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Transform /^customer "([^"]+)"$/
     * @Transform /^email "([^"]+)"$/
     */
    public function getCustomerByEmail($email)
    {
        $customer = $this->customerRepository->findCustomerByEmail($email);

        Assert::isInstanceOf($customer, CustomerInterface::class);

        return $customer;
    }

    /**
     * @Transform /^customer$/
     */
    public function customer()
    {
        $customer = $this->sharedStorage->get('customer');

        Assert::isInstanceOf($customer, CustomerInterface::class);

        return $customer;
    }

    /**
     * @Transform /^customer "([^"]+)"$/
     * @Transform /^username "([^"]+)"$/
     */
    public function getCustomerByUsername($username)
    {
        $customer = $this->customerRepository->findCustomerByUsername($username);

        Assert::isInstanceOf($customer, CustomerInterface::class);

        return $customer;
    }
}
