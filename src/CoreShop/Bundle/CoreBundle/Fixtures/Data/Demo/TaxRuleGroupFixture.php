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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaxRuleGroupFixture extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        private RepositoryInterface $taxRuleGroupRepository,
        private FactoryInterface $taxRuleGroupFactory,
        private FactoryInterface $taxRuleFactory,
        private CountryRepositoryInterface $countryRepository,
    ) {
    }

    public static function getGroups(): array
    {
        return ['demo'];
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            TaxRateFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->taxRuleGroupRepository->findAll())) {
            /**
             * @var TaxRuleGroupInterface $taxRuleGroup
             */
            $taxRuleGroup = $this->taxRuleGroupFactory->createNew();
            $taxRuleGroup->setName('AT');
            $taxRuleGroup->setActive(true);

            /**
             * @var TaxRuleInterface $taxRule
             */
            $taxRule = $this->taxRuleFactory->createNew();
            $taxRule->setCountry($this->countryRepository->findByCode('AT'));
            $taxRule->setTaxRate($this->getReference('taxRate'));
            $taxRule->setTaxRuleGroup($taxRuleGroup);
            $taxRule->setBehavior(TaxCalculatorInterface::DISABLE_METHOD);

            $manager->persist($taxRuleGroup);
            $manager->persist($taxRule);
            $manager->flush();

            $this->setReference('taxRule', $taxRuleGroup);
        }
    }
}
