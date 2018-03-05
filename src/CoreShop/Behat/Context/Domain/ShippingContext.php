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
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Taxation\Repository\TaxRateRepositoryInterface;
use Pimcore\Model\DataObject\Folder;
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
     * @param SharedStorageInterface $sharedStorage
     * @param CarrierRepositoryInterface $carrierRepository
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     * @param FactoryInterface $addressFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CarrierRepositoryInterface $carrierRepository,
        RuleValidationProcessorInterface $ruleValidationProcessor,
        FactoryInterface $addressFactory
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->carrierRepository = $carrierRepository;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->addressFactory = $addressFactory;
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
     * @Then /^the (shipping rule "[^"]+") for (my cart) with (carrier "[^"]+")$/
     * @Then /^the (shipping rule) should be valid for (my cart) with (carrier "[^"]+")$/
     */
    public function theShippingRuleShouldBeValid(ShippingRuleInterface $rule, CartInterface $cart, CarrierInterface $carrier)
    {
        $address = $this->addressFactory->createNew();

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
        $address = $this->addressFactory->createNew();

        Assert::false($this->ruleValidationProcessor->isValid($carrier, $rule, [
            'shippable' => $cart,
            'address' => $address
        ]));
    }
}
