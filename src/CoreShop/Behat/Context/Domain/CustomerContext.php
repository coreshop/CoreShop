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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use Webmozart\Assert\Assert;

final class CustomerContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private CustomerContextInterface $customerContext;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CustomerContextInterface $customerContext
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerContext = $customerContext;
    }

    /**
     * @Then /^I should be logged in with (email "[^"]+")$/
     */
    public function iShouldBeLoggedInWithEmail(CustomerInterface $customer)
    {
        Assert::same(
            $customer->getId(),
            $this->customerContext->getCustomer()->getId(),
            sprintf(
                'Given customer (%s) is different from logged in customer (%s)',
                $customer->getEmail(),
                $this->customerContext->getCustomer()->getEmail()
            )
        );
    }

    /**
     * @Then /^It should throw an error deleting the (customer "[^"]+")$/
     */
    public function itShouldThrowAnErrorDeletingCustomer(CustomerInterface $customer)
    {
        Assert::throws(function () use ($customer) {
            $customer->delete();
        });
    }

    /**
     * @Then /^It should not throw an error deleting the (customer "[^"]+")$/
     */
    public function itShouldNotThrowAnErrorDeletingTheCustomer(CustomerInterface $customer)
    {
        $customer->delete();
    }
}
