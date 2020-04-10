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

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\Checkout\AddressPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\CustomerPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\PaymentPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\ShippingPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\SummaryPageInterface;
use CoreShop\Behat\Page\Frontend\Checkout\ThankYouPageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;

final class CheckoutContext implements Context
{
    private $sharedStorage;
    private $linkGenerator;
    private $customerPage;
    private $addressPage;
    private $shippingPage;
    private $paymentPage;
    private $summaryPage;
    private $thankYouPage;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        LinkGeneratorInterface $linkGenerator,
        CustomerPageInterface $customerPage,
        AddressPageInterface $addressPage,
        ShippingPageInterface $shippingPage,
        PaymentPageInterface $paymentPage,
        SummaryPageInterface $summaryPage,
        ThankYouPageInterface $thankYouPage
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->linkGenerator = $linkGenerator;
        $this->customerPage = $customerPage;
        $this->addressPage = $addressPage;
        $this->shippingPage = $shippingPage;
        $this->paymentPage = $paymentPage;
        $this->summaryPage = $summaryPage;
        $this->thankYouPage = $thankYouPage;
    }

    /**
     * @When I am at the address checkout step
     */
    public function IAmAtTheAddressCheckoutStep()
    {
        $this->addressPage->open();
    }

    /**
     * @When I should be on the address checkout step
     */
    public function IShouldBeOnTheAddressCheckoutStep()
    {
        $this->addressPage->verify();
    }

    /**
     * @When /^I use the last (address) as invoice address$/
     */
    public function IUseTheLastAddressAsInvoiceAddress(AddressInterface $address)
    {
        $this->addressPage->useInvoiceAddress($address);
    }

    /**
     * @When /^I submit the address step$/
     */
    public function ISubmitTheAddressStep()
    {
        $this->addressPage->submitStep();
    }

    /**
     * @When I am at the customer checkout step
     */
    public function IAmAtTheCustomerCheckoutStep()
    {
        $this->customerPage->open();
    }

    /**
     * @When I should be on the customer checkout step
     */
    public function IShouldBeOnTheCustomerCheckoutStep()
    {
        $this->customerPage->verify();
    }

    /**
     * @When I am at the shipping checkout step
     */
    public function IAmAtTheShippingCheckoutStep()
    {
        $this->shippingPage->open();
    }

    /**
     * @When I should be on the shipping checkout step
     */
    public function IShouldBeOnTheShippingCheckoutStep()
    {
        $this->shippingPage->verify();
    }

    /**
     * @When /^I submit the shipping step$/
     */
    public function ISubmitTheShippingStep()
    {
        $this->shippingPage->submitStep();
    }

    /**
     * @When I am at the payment checkout step
     */
    public function IAmAtThePaymentCheckoutStep()
    {
        $this->paymentPage->open();
    }

    /**
     * @When I should be on the payment checkout step
     */
    public function IShouldBeOnThePaymentCheckoutStep()
    {
        $this->paymentPage->verify();
    }

    /**
     * @When /^I select the (payment provider "[^"]+")$/
     */
    public function ISelectThePaymentProvider(PaymentProviderInterface $provider)
    {
        $this->paymentPage->selectPaymentProvider($provider);
    }

    /**
     * @When /^I submit the payment step$/
     */
    public function ISubmitThePaymentStep()
    {
        $this->paymentPage->submitStep();
    }

    /**
     * @When I am at the summary checkout step
     */
    public function IAmAtTheSummaryCheckoutStep()
    {
        $this->summaryPage->open();
    }

    /**
     * @When I should be on the summary checkout step
     */
    public function IShouldBeOnTheSummaryCheckoutStep()
    {
        $this->summaryPage->verify();
    }

    /**
     * @When I accept the checkout terms of service
     */
    public function IAcceptTheTermsOfService()
    {
        $this->summaryPage->acceptTermsOfService();
    }

    /**
     * @When I decline the checkout terms of service
     */
    public function IDeclineTheTermsOfService()
    {
        $this->summaryPage->declineTermsOfService();
    }

    /**
     * @When I submit the order
     */
    public function ISubmitTheOrder()
    {
        $this->summaryPage->submitOrder();
    }

    /**
     * @When I submit the quote
     */
    public function ISubmitTheQuote()
    {
        $this->summaryPage->submitQuote();
    }

    /**
     * @When I should be on the thank you page
     */
    public function IShouldBeOnTheThankYouPage()
    {
        $this->thankYouPage->verify();
    }
}
