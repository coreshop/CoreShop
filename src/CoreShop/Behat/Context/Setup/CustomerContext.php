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
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Customer\Context\FixedCustomerContext;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;

final class CustomerContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private FactoryInterface $customerFactory;
    private FactoryInterface $userFactory;
    private FixedCustomerContext $fixedCustomerContext;
    private FactoryInterface $addressFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $customerFactory,
        FactoryInterface $userFactory,
        FixedCustomerContext $fixedCustomerContext,
        FactoryInterface $addressFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
        $this->fixedCustomerContext = $fixedCustomerContext;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @Given /^the site has a customer "([^"]+)"$/
     */
    public function theSiteHasACustomer(string $email): void
    {
        $category = $this->createCustomer($email);

        $this->saveCustomer($category);
    }

    /**
     * @Given /^the site has a customer "([^"]+)" with password "([^"]+)"$/
     * @Given /^the site has a customer "([^"]+)" with password "([^"]+)" and name "([^"]+)" "([^"]+)"$/
     */
    public function theSiteHasACustomerWithPassword(string $email, string $password, ?string $firstname = null, ?string $lastname = null): void
    {
        $customer = $this->createCustomer($email);

        $customer->getUser()->setPassword($password);

        if ($firstname) {
            $customer->setFirstname($firstname);
        }

        if ($lastname) {
            $customer->setLastname($lastname);
        }

        $this->saveCustomer($customer);
    }

    /**
     * @Then /^the (customer "[^"]+") was deleted$/
     */
    public function accountWasDeleted(CustomerInterface $customer): void
    {
        $customer->delete();
    }

    /**
     * @Then /^the (customer "[^"]+") is in (customer-group "[^"]+")$/
     * @Then /^([^"]+) is in (customer-group "[^"]+")$/
     */
    public function theCustomerIsInGroup(CustomerInterface $customer, CustomerGroupInterface $group): void
    {
        $customer->setCustomerGroups([$group]);

        $this->saveCustomer($customer);
    }

    /**
     * @Given /^I am (customer "[^"]+")$/
     */
    public function iAmCustomer(CustomerInterface $customer): void
    {
        $this->fixedCustomerContext->setCustomer($customer);
    }

    /**
     * @Given /^the (customer "[^"]+") has an address with (country "[^"]+"), "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     * @Given /^the (customer) has an address with (country "[^"]+"), "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function theCustomerHasAnAddress(
        CustomerInterface $customer,
        CountryInterface $country,
        $postcode,
        $city,
        $street,
        $nr
    ): void {
        /**
         * @var AddressInterface $address
         */
        $address = $this->addressFactory->createNew();
        $address->setCountry($country);
        $address->setPostcode($postcode);
        $address->setCity($city);
        $address->setStreet($street);
        $address->setNumber($nr);
        $address->setKey(uniqid());
        $address->setPublished(true);
        $address->setParent($customer);
        $address->save();

        $customer->addAddress($address);
        $customer->save();

        $this->sharedStorage->set('address', $address);
    }

    private function createCustomer(string $email): CustomerInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        [$firstname, $lastname] = explode('@', $email);

        $customer->setPublished(true);
        $customer->setKey(File::getValidFilename($email));
        $customer->setParent(Folder::getByPath('/'));
        $customer->setEmail($email);
        $customer->setFirstname($firstname);
        $customer->setLastname($lastname);

        /**
         * @var UserInterface $user
         */
        $user = $this->userFactory->createNew();
        $user->setKey(File::getValidFilename($email));
        $user->setParent($customer);
        $user->setPublished(true);
        $user->setLoginIdentifier($email);
        $user->setCustomer($customer);

        $customer->setUser($user);

        return $customer;
    }

    private function saveCustomer(CustomerInterface $customer): void
    {
        $user = $customer->getUser();
        $customer->setUser(null);
        $customer->save();

        if ($user) {
            $user->setParent($customer);
            $user->save();

            $customer->setUser($user);
            $customer->save();

            $this->sharedStorage->set('user', $user);
        }

        $this->sharedStorage->set('customer', $customer);
    }
}
