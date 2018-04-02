<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
use Webmozart\Assert\Assert;

final class ShippingContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var RuleValidationProcessorInterface
     */
    private $ruleValidationProcessor;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var CarrierPriceCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CarrierRepositoryInterface $carrierRepository
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     * @param FactoryInterface $addressFactory
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CarrierRepositoryInterface $carrierRepository,
        RuleValidationProcessorInterface $ruleValidationProcessor,
        FactoryInterface $addressFactory,
        CarrierPriceCalculatorInterface $carrierPriceCalculator
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->carrierRepository = $carrierRepository;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->addressFactory = $addressFactory;
        $this->carrierPriceCalculator = $carrierPriceCalculator;
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
    public function theShippingRuleShouldBeValid(ShippingRuleInterface $rule, CartInterface $cart, CarrierInterface $carrier)
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::true($this->ruleValidationProcessor->isValid($carrier, $rule, [
            'shippable' => $cart,
            'address' => $address
        ]));
    }

    /**
     * @Then /^the (shipping rule "[^"]+") should be invalid for (my cart) with (carrier "[^"]+")$/
     * @Then /^the (shipping rule) should be invalid for (my cart) with (carrier "[^"]+")$/
     */
    public function theShippingRuleShouldBeInvalid(ShippingRuleInterface $rule, CartInterface $cart, CarrierInterface $carrier)
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::false($this->ruleValidationProcessor->isValid($carrier, $rule, [
            'shippable' => $cart,
            'address' => $address
        ]));
    }

    /**
     * @Then /^shipping for (my cart) with (carrier "[^"]+") should be priced at "([^"]+)"$/
     */
    public function shippingShouldBePriced(CartInterface $cart, CarrierInterface $carrier, int $price)
    {
        $address = $cart->getShippingAddress() ?: $this->addressFactory->createNew();

        Assert::same(intval($price), $this->carrierPriceCalculator->getPrice($carrier, $cart, $address));
    }
}
