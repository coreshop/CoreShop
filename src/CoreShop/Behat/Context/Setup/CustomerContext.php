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
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Context\FixedCustomerContext;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;

final class CustomerContext implements Context
{
    private $sharedStorage;
    private $customerFactory;
    private $fixedCustomerContext;
    private $addressFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $customerFactory,
        FixedCustomerContext $fixedCustomerContext,
        FactoryInterface $addressFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerFactory = $customerFactory;
        $this->fixedCustomerContext = $fixedCustomerContext;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @Given /^the site has a customer "([^"]+)"$/
     */
    public function theSiteHasACustomer(string $email)
    {
        $category = $this->createCustomer($email);

        $this->saveCustomer($category);
    }

    /**
     * @Then /^the (customer "[^"]+") is in (customer-group "[^"]+")$/
     * @Then /^([^"]+) is in (customer-group "[^"]+")$/
     */
    public function theCustomerIsInGroup(CustomerInterface $customer, CustomerGroupInterface $group)
    {
        $customer->setCustomerGroups([$group]);

        $this->saveCustomer($customer);
    }

    /**
     * @Given /^I am (customer "[^"]+")$/
     */
    public function iAmCustomer(CustomerInterface $customer)
    {
        $this->fixedCustomerContext->setCustomer($customer);
    }

    /**
     * @Given /^the (customer "[^"]+") has an address with (country "[^"]+"), "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function theCustomerHasAnAddress(
        CustomerInterface $customer,
        CountryInterface $country,
        $postcode,
        $city,
        $street,
        $nr
    ) {
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
    }

    /**
     * @param string $email
     *
     * @return CustomerInterface
     */
    private function createCustomer(string $email)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setKey(File::getValidFilename($email));
        $customer->setParent(Folder::getByPath('/'));
        $customer->setEmail($email);
        $customer->setFirstname(reset(explode('@', $email)));
        $customer->setLastname(end(explode('@', $email)));

        return $customer;
    }

    /**
     * @param CustomerInterface $customer
     */
    private function saveCustomer(CustomerInterface $customer)
    {
        $customer->save();

        $this->sharedStorage->set('customer', $customer);
    }
}
