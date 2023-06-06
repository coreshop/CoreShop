<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Payment\Calculator\PaymentProviderRulePriceCalculator;
use CoreShop\Component\Payment\Model\PaymentProviderRuleInterface;
use CoreShop\Component\Payment\Validator\PaymentProviderRuleValidator;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use Webmozart\Assert\Assert;

final class PaymentProviderContext implements Context
{
    public function __construct(
        private PaymentProviderRepositoryInterface $paymentProviderRepository,
        private RuleValidationProcessorInterface $ruleValidationProcessor,
        private FactoryInterface $addressFactory,
        private PaymentProviderRulePriceCalculator $paymentProviderPriceCalculator,
        private PaymentProviderRuleValidator $paymentRuleValidator,
        private CartContextResolverInterface $cartContextResolver,
    ) {
    }

    /**
     * @Then /^there should be a payment provider "([^"]+)"$/
     */
    public function thereShouldBeAPaymentProvider($name): void
    {
        $paymentProviders = $this->paymentProviderRepository->findBy(['identifier' => $name]);

        Assert::eq(
            count($paymentProviders),
            1,
            sprintf('%d payment providers has been found with name "%s".', count($paymentProviders), $name),
        );
    }

    /**
     * @Then /^the (payment-provider-rule "[^"]+") should be valid for (my cart) with (payment provider "[^"]+")$/
     * @Then /^the (payment-provider-rule) should be valid for (my cart) with (payment provider "[^"]+")$/
     */
    public function thePaymentProviderRuleShouldBeValid(PaymentProviderRuleInterface $rule, OrderInterface $cart, PaymentProviderInterface $paymentProvider): void
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::true($this->ruleValidationProcessor->isValid($paymentProvider, $rule, [
            'payable' => $cart,
            'address' => $address,
        ]));
    }

    /**
     * @Then /^the (payment-provider-rule "[^"]+") should be invalid for (my cart) with (payment provider "[^"]+")$/
     * @Then /^the (payment-provider-rule) should be invalid for (my cart) with (payment provider "[^"]+")$/
     */
    public function thePaymentProviderRuleShouldBeInvalid(PaymentProviderRuleInterface $rule, OrderInterface $cart, PaymentProviderInterface $paymentProvider): void
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::false($this->ruleValidationProcessor->isValid($paymentProvider, $rule, [
            'payable' => $cart,
            'address' => $address,
        ]));
    }

    /**
     * @Then /^payment for (my cart) with (payment provider "[^"]+") should be priced at "([^"]+)"$/
     */
    public function paymentShouldBePriced(OrderInterface $cart, PaymentProviderInterface $paymentProvider, int $price): void
    {
        Assert::same($price, $this->paymentProviderPriceCalculator->getPrice($paymentProvider, $cart, $this->cartContextResolver->resolveCartContext($cart)));
    }

    /**
     * @Then /^the (payment provider "[^"]+") should be valid for (my cart)$/
     */
    public function paymentProviderShouldBeValidForMyCart(PaymentProviderInterface $paymentProvider, OrderInterface $cart): void
    {
        $ruleResult = $this->paymentRuleValidator->isPaymentProviderRuleValid($paymentProvider, $cart);

        Assert::true(
            $ruleResult,
            sprintf('Asserted that the PaymentProvider %s is valid for my cart, but it is not', $paymentProvider->getTitle('en')),
        );
    }
}
