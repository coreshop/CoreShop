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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use Webmozart\Assert\Assert;

final class ShippingContext implements Context
{
    public function __construct(
        private CarrierRepositoryInterface $carrierRepository,
        private RuleValidationProcessorInterface $ruleValidationProcessor,
        private FactoryInterface $addressFactory,
        private CarrierPriceCalculatorInterface $carrierPriceCalculator,
        private ShippableCarrierValidatorInterface $shippingRuleValidator,
        private CartContextResolverInterface $cartContextResolver,
    ) {
    }

    /**
     * @Then /^there should be a carrier "([^"]+)"$/
     */
    public function thereShouldBeACarrier($name): void
    {
        $carriers = $this->carrierRepository->findBy(['name' => $name]);

        Assert::eq(
            count($carriers),
            1,
            sprintf('%d carriers has been found with name "%s".', count($carriers), $name),
        );
    }

    /**
     * @Then /^the (shipping rule "[^"]+") should be valid for (my cart) with (carrier "[^"]+")$/
     * @Then /^the (shipping rule) should be valid for (my cart) with (carrier "[^"]+")$/
     */
    public function theShippingRuleShouldBeValid(ShippingRuleInterface $rule, OrderInterface $cart, CarrierInterface $carrier): void
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::true($this->ruleValidationProcessor->isValid($carrier, $rule, [
            'shippable' => $cart,
            'address' => $address,
        ]));
    }

    /**
     * @Then /^the (shipping rule "[^"]+") should be invalid for (my cart) with (carrier "[^"]+")$/
     * @Then /^the (shipping rule) should be invalid for (my cart) with (carrier "[^"]+")$/
     */
    public function theShippingRuleShouldBeInvalid(ShippingRuleInterface $rule, OrderInterface $cart, CarrierInterface $carrier): void
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::false($this->ruleValidationProcessor->isValid($carrier, $rule, [
            'shippable' => $cart,
            'address' => $address,
        ]));
    }

    /**
     * @Then /^shipping for (my cart) with (carrier "[^"]+") should be priced at "([^"]+)"$/
     */
    public function shippingShouldBePriced(OrderInterface $cart, CarrierInterface $carrier, int $price): void
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::same($price, $this->carrierPriceCalculator->getPrice($carrier, $cart, $address, $this->cartContextResolver->resolveCartContext($cart)));
    }

    /**
     * @Then /^the (carrier "[^"]+") should be valid for (my cart)$/
     */
    public function carrierShouldBeValidForMyCart(CarrierInterface $carrier, OrderInterface $cart): void
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        $ruleResult = $this->shippingRuleValidator->isCarrierValid($carrier, $cart, $address);

        Assert::true(
            $ruleResult,
            sprintf('Asserted that the Carrier %s is valid for my cart, but it is not', $carrier->getTitle('en')),
        );
    }
}
