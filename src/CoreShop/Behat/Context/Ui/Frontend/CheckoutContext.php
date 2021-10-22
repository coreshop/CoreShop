<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\Checkout\AddressPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\CustomerPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\PaymentPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\ShippingPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\SummaryPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\ThankYouPageInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use Webmozart\Assert\Assert;

final class CheckoutContext implements Context
{
    public function __construct(private CustomerPageInterface $customerPage, private AddressPageInterface $addressPage, private ShippingPageInterface $shippingPage, private PaymentPageInterface $paymentPage, private SummaryPageInterface $summaryPage, private ThankYouPageInterface $thankYouPage)
    {
    }

    /**
     * @When I am at the address checkout step
     * @When I try to open the address checkout step
     */
    public function IAmAtTheAddressCheckoutStep(): void
    {
        $this->addressPage->tryToOpen();
    }

    /**
     * @When I should be on the address checkout step
     */
    public function IShouldBeOnTheAddressCheckoutStep(): void
    {
        $this->addressPage->verify();
    }

    /**
     * @When /^I use the last (address) as invoice address$/
     */
    public function IUseTheLastAddressAsInvoiceAddress(AddressInterface $address): void
    {
        $this->addressPage->useInvoiceAddress($address);
    }

    /**
     * @When /^I submit the address step$/
     */
    public function ISubmitTheAddressStep(): void
    {
        $this->addressPage->submitStep();
    }

    /**
     * @When I am at the customer checkout step
     * @When I try to open the customer checkout step
     */
    public function IAmAtTheCustomerCheckoutStep(): void
    {
        $this->customerPage->tryToOpen();
    }

    /**
     * @When I should be on the customer checkout step
     */
    public function IShouldBeOnTheCustomerCheckoutStep(): void
    {
        $this->customerPage->tryToOpen();
    }

    /**
     * @When I am at the shipping checkout step
     * @When I try to open the shipping checkout step
     */
    public function IAmAtTheShippingCheckoutStep(): void
    {
        $this->shippingPage->tryToOpen();
    }

    /**
     * @When I should be on the shipping checkout step
     */
    public function IShouldBeOnTheShippingCheckoutStep(): void
    {
        $this->shippingPage->verify();
    }

    /**
     * @When /^I should not see carrier "([^"]+)"$/
     */
    public function IShouldNotSeeCarrier($carrier): void
    {
        Assert::false(in_array($carrier, $this->shippingPage->getCarriers(), true));
    }

    /**
     * @When /^I submit the shipping step$/
     */
    public function ISubmitTheShippingStep(): void
    {
        $this->shippingPage->submitStep();
    }

    /**
     * @When I am at the payment checkout step
     * @When I try to open the payment checkout step
     */
    public function IAmAtThePaymentCheckoutStep(): void
    {
        $this->paymentPage->tryToOpen();
    }

    /**
     * @When I should be on the payment checkout step
     */
    public function IShouldBeOnThePaymentCheckoutStep(): void
    {
        $this->paymentPage->verify();
    }

    /**
     * @When /^I select the (payment provider "[^"]+")$/
     */
    public function ISelectThePaymentProvider(PaymentProviderInterface $provider): void
    {
        $this->paymentPage->selectPaymentProvider($provider);
    }

    /**
     * @When /^I submit the payment step$/
     */
    public function ISubmitThePaymentStep(): void
    {
        $this->paymentPage->submitStep();
    }

    /**
     * @When I am at the summary checkout step
     * @When I try to open the summary checkout step
     */
    public function IAmAtTheSummaryCheckoutStep(): void
    {
        $this->summaryPage->tryToOpen();
    }

    /**
     * @When I should be on the summary checkout step
     */
    public function IShouldBeOnTheSummaryCheckoutStep(): void
    {
        $this->summaryPage->verify();
    }

    /**
     * @When I accept the checkout terms of service
     */
    public function IAcceptTheTermsOfService(): void
    {
        $this->summaryPage->acceptTermsOfService();
    }

    /**
     * @When I decline the checkout terms of service
     */
    public function IDeclineTheTermsOfService(): void
    {
        $this->summaryPage->declineTermsOfService();
    }

    /**
     * @When I submit the order
     */
    public function ISubmitTheOrder(): void
    {
        $this->summaryPage->submitOrder();
    }

    /**
     * @When I submit the quote
     */
    public function ISubmitTheQuote(): void
    {
        $this->summaryPage->submitQuote();
    }

    /**
     * @When I should be on the thank you page
     */
    public function IShouldBeOnTheThankYouPage(): void
    {
        $this->thankYouPage->verify();
    }

    /**
     * @Given I specify the guest checkout firstname :firstname
     */
    public function iSpecifyTheGuestCheckoutFirstname(?string $firstname = null): void
    {
        $this->customerPage->specifyGuestFirstname($firstname);
    }

    /**
     * @Given I specify the guest checkout lastname :lastname
     */
    public function iSpecifyTheGuestCheckoutLastname(?string $lastname = null): void
    {
        $this->customerPage->specifyGuestLastname($lastname);
    }

    /**
     * @Given I specify the guest checkout email address :email
     */
    public function iSpecifyTheGuestCheckoutEmail(?string $email = null): void
    {
        $this->customerPage->specifyGuestEmail($email);
        $this->customerPage->specifyGuestEmailRepeat($email);
    }

    /**
     * @Given I specify the guest checkout address firstname :firstname
     */
    public function iSpecifyTheGuestCheckoutAddressFirstname(?string $firstname = null): void
    {
        $this->customerPage->specifyGuestAddressFirstname($firstname);
    }

    /**
     * @Given I specify the guest checkout address lastname :lastname
     */
    public function iSpecifyTheGuestCheckoutAddressLastname(?string $lastname = null): void
    {
        $this->customerPage->specifyGuestAddressLastname($lastname);
    }

    /**
     * @Given I specify the guest checkout address street :street
     */
    public function iSpecifyTheGuestCheckoutAddressStreet(?string $street = null): void
    {
        $this->customerPage->specifyGuestAddressStreet($street);
    }

    /**
     * @Given I specify the guest checkout address number :number
     */
    public function iSpecifyTheGuestCheckoutAddressNumber(?string $number = null): void
    {
        $this->customerPage->specifyGuestAddressNumber($number);
    }

    /**
     * @Given I specify the guest checkout address postcode :postcode
     */
    public function iSpecifyTheGuestCheckoutAddressPostcode(?string $postcode = null): void
    {
        $this->customerPage->specifyGuestAddressPostcode($postcode);
    }

    /**
     * @Given I specify the guest checkout address city :city
     */
    public function iSpecifyTheGuestCheckoutAddressCity(?string $city = null): void
    {
        $this->customerPage->specifyGuestAddressCity($city);
    }

    /**
     * @Given I specify the guest checkout address phone number :number
     */
    public function iSpecifyTheGuestCheckoutAddressPhoneNumber(?string $number = null): void
    {
        $this->customerPage->specifyGuestAddressPhoneNumber($number);
    }

    /**
     * @Given /^I specify the guest checkout address (country "[^"]+")$/
     */
    public function iSpecifyTheAddressCountry(?CountryInterface $country = null): void
    {
        $this->customerPage->specifyGuestAddressCountry($country ? $country->getId() : null);
    }

    /**
     * @Given I accept the guest checkout terms of service
     */
    public function iAcceptTheGuestCheckoutTermsOfService(): void
    {
        $this->customerPage->acceptTermsOfService();
    }

    /**
     * @When I submit the guest checkout
     */
    public function iSubmitTheGuestCheckout(): void
    {
        $this->customerPage->submitGuestCheckout();
    }
}
