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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Repository\TaxRuleRepositoryInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxRuleGroupContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var RepositoryInterface
     */
    private $taxRuleGroupRepository;

    /**
     * @var TaxRuleRepositoryInterface
     */
    private $taxRuleRepository;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @param SharedStorageInterface        $sharedStorage
     * @param FactoryInterface              $addressFactory
     * @param RepositoryInterface           $taxRuleGroupRepository
     * @param TaxRuleRepositoryInterface    $taxRuleRepository
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param CountryContextInterface       $countryContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $addressFactory,
        RepositoryInterface $taxRuleGroupRepository,
        TaxRuleRepositoryInterface $taxRuleRepository,
        TaxCalculatorFactoryInterface $taxCalculatorFactory,
        CountryContextInterface $countryContext
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->addressFactory = $addressFactory;
        $this->taxRuleGroupRepository = $taxRuleGroupRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->countryContext = $countryContext;
    }

    /**
     * @Then /^there should be a tax rule group "([^"]+)" with "([^"]+)" (?:rule|rules)$/
     */
    public function thereShouldBeATaxRuleGroupWithXRules($name, $countOfRules)
    {
        $groups = $this->taxRuleGroupRepository->findBy(['name' => $name]);

        Assert::eq(
            count($groups),
            1,
            sprintf('%d tax rule groups has been found with name "%s".', count($groups), $name)
        );

        /**
         * @var TaxRuleGroupInterface $group
         */
        $group = reset($groups);

        $rules = $this->taxRuleRepository->findByGroup($group);

        Assert::eq(
            count($rules),
            $countOfRules,
            sprintf('Found %d rules instead of expected %d', count($group->getTaxRules()), $countOfRules)
        );
    }

    /**
     * @Then /^the (tax rule group "[^"]+") should add "([^"]+)" to the price "([^"]+)"$/
     * @Then /^the (tax rule group) should add "([^"]+)" to the price "([^"]+)"$/
     */
    public function taxRuleShouldTaxThePrice(TaxRuleGroupInterface $taxRuleGroup, $tax, $price)
    {
        $address = $this->addressFactory->createNew();
        $address->setCountry($this->countryContext->getCountry());

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);

        $priceWithTax = $taxCalculator->applyTaxes($price);
        $taxAmount = $priceWithTax - $price;

        Assert::eq(
            $taxAmount,
            $tax,
            sprintf('The tax %s is different to given tax of %s', $taxAmount, $tax)
        );
    }

    /**
     * @Then /^the tax calculator should be null for (tax rule group) in (country "[^"]+")$/
     */
    public function taxCalculatorShouldBeNull(TaxRuleGroupInterface $taxRuleGroup, CountryInterface $country)
    {
        $address = $this->addressFactory->createNew();
        $address->setCountry($country);

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);

        Assert::null($taxCalculator);
    }
}
