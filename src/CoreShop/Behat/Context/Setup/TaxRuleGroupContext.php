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
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use Doctrine\Persistence\ObjectManager;

final class TaxRuleGroupContext implements Context
{
    private $sharedStorage;
    private $objectManager;
    private $taxRuleGroupFactory;
    private $taxRuleFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $taxRuleGroupFactory,
        FactoryInterface $taxRuleFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->taxRuleGroupFactory = $taxRuleGroupFactory;
        $this->taxRuleFactory = $taxRuleFactory;
    }

    /**
     * @Given /^the site has a tax rule group "([^"]+)"$/
     */
    public function theSiteHasATaxRuleGroup($name)
    {
        $this->createTaxRuleGroup($name);
    }

    /**
     * @Given /^the (tax rule group "[^"]+") has a tax rule for (country "[^"]+") with (tax rate "[^"]+")$/
     * @Given /^the ([^"]+) has a tax rule for (country "[^"]+") with (tax rate "[^"]+")$/
     */
    public function theTaxRuleGroupHasATaxRuleForCountryWithTax(TaxRuleGroupInterface $taxRuleGroup, CountryInterface $country, TaxRateInterface $taxRate)
    {
        /**
         * @var TaxRuleInterface $taxRule
         */
        $taxRule = $this->taxRuleFactory->createNew();
        $taxRule->setTaxRuleGroup($taxRuleGroup);
        $taxRule->setTaxRate($taxRate);
        $taxRule->setCountry($country);
        $taxRule->setBehavior(TaxCalculatorInterface::DISABLE_METHOD);

        $this->objectManager->persist($taxRule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (tax rule group "[^"]+") has a tax rule for (country "[^"]+") with (tax rate "[^"]+") and it combines all rules$/
     * @Given /^([^"]+) has a tax rule for (country "[^"]+") with (tax rate "[^"]+") and it combines all rules$/
     */
    public function theTaxRuleGroupHasATaxRuleForCountryWithTaxAndCombination(TaxRuleGroupInterface $taxRuleGroup, CountryInterface $country, TaxRateInterface $taxRate)
    {
        /**
         * @var TaxRuleInterface $taxRule
         */
        $taxRule = $this->taxRuleFactory->createNew();
        $taxRule->setTaxRuleGroup($taxRuleGroup);
        $taxRule->setTaxRate($taxRate);
        $taxRule->setCountry($country);
        $taxRule->setBehavior(TaxCalculatorInterface::COMBINE_METHOD);

        $this->objectManager->persist($taxRule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (tax rule group "[^"]+") has a tax rule for (country "[^"]+") with (tax rate "[^"]+") and it calculates them one after another$/
     * @Given /^([^"]+) has a tax rule for (country "[^"]+") with (tax rate "[^"]+") and it calculates them one after another$/
     */
    public function theTaxRuleGroupHasATaxRuleForCountryWithTaxAndOneAfterAnother(TaxRuleGroupInterface $taxRuleGroup, CountryInterface $country, TaxRateInterface $taxRate)
    {
        /**
         * @var TaxRuleInterface $taxRule
         */
        $taxRule = $this->taxRuleFactory->createNew();
        $taxRule->setTaxRuleGroup($taxRuleGroup);
        $taxRule->setTaxRate($taxRate);
        $taxRule->setCountry($country);
        $taxRule->setBehavior(TaxCalculatorInterface::ONE_AFTER_ANOTHER_METHOD);

        $this->objectManager->persist($taxRule);
        $this->objectManager->flush();
    }

    /**
     * @param string $name
     */
    private function createTaxRuleGroup($name)
    {
        /**
         * @var TaxRuleGroupInterface $taxRule
         */
        $taxRule = $this->taxRuleGroupFactory->createNew();
        $taxRule->setName($name);

        $this->saveTaxRuleGroup($taxRule);
    }

    /**
     * @param TaxRuleGroupInterface $taxRuleGroup
     */
    private function saveTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup)
    {
        $this->objectManager->persist($taxRuleGroup);
        $this->objectManager->flush();

        $this->sharedStorage->set('taxRuleGroup', $taxRuleGroup);
    }
}
