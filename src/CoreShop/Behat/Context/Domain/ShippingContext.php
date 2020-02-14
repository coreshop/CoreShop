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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use Webmozart\Assert\Assert;

final class ShippingContext implements Context
{
    private $sharedStorage;
    private $carrierRepository;
    private $ruleValidationProcessor;
    private $addressFactory;
    private $carrierPriceCalculator;
    private $shippingRuleValidator;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CarrierRepositoryInterface $carrierRepository,
        RuleValidationProcessorInterface $ruleValidationProcessor,
        FactoryInterface $addressFactory,
        CarrierPriceCalculatorInterface $carrierPriceCalculator,
        ShippableCarrierValidatorInterface $shippingRuleValidator
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->carrierRepository = $carrierRepository;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->addressFactory = $addressFactory;
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->shippingRuleValidator = $shippingRuleValidator;
    }

    /**
     * @Then /^there should be a carrier "([^"]+)"$/
     */
    public function thereShouldBeACarrier($name)
    {
        $carriers = $this->carrierRepository->findBy(['name' => $name]);

        Assert::eq(
            count($carriers),
            1,
            sprintf('%d carriers has been found with name "%s".', count($carriers), $name)
        );
    }

    /**
     * @Then /^the (shipping rule "[^"]+") should be valid for (my cart) with (carrier "[^"]+")$/
     * @Then /^the (shipping rule) should be valid for (my cart) with (carrier "[^"]+")$/
     */
    public function theShippingRuleShouldBeValid(
        ShippingRuleInterface $rule,
        CartInterface $cart,
        CarrierInterface $carrier
    ) {
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
    public function theShippingRuleShouldBeInvalid(
        ShippingRuleInterface $rule,
        CartInterface $cart,
        CarrierInterface $carrier
    ) {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::false($this->ruleValidationProcessor->isValid($carrier, $rule, [
            'shippable' => $cart,
            'address' => $address,
        ]));
    }

    /**
     * @Then /^shipping for (my cart) with (carrier "[^"]+") should be priced at "([^"]+)"$/
     */
    public function shippingShouldBePriced(CartInterface $cart, CarrierInterface $carrier, int $price)
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::same((int)$price, $this->carrierPriceCalculator->getPrice($carrier, $cart, $address));
    }

    /**
     * @Then /^the (carrier "[^"]+") should be valid for (my cart)$/
     */
    public function carrierShouldBeValidForMyCart(CarrierInterface $carrier, CartInterface $cart)
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        $ruleResult = $this->shippingRuleValidator->isCarrierValid($carrier, $cart, $address);

        Assert::true(
            $ruleResult,
            sprintf('Asserted that the Carrier %s is valid for my cart, but it is not', $carrier->getTitle('en'))
        );
    }
}
