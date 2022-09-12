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

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaxRuleGroupFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
{
    private ?ContainerInterface $container;

    public function getVersion(): string
    {
        return '2.0';
    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
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
        if (!count($this->container->get('coreshop.repository.tax_rule_group')->findAll())) {
            /**
             * @var TaxRuleGroupInterface $taxRuleGroup
             */
            $taxRuleGroup = $this->container->get('coreshop.factory.tax_rule_group')->createNew();
            $taxRuleGroup->setName('AT');
            $taxRuleGroup->setActive(true);

            /**
             * @var TaxRuleInterface $taxRule
             */
            $taxRule = $this->container->get('coreshop.factory.tax_rule')->createNew();
            $taxRule->setCountry($this->container->get('coreshop.repository.country')->findByCode('AT'));
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
