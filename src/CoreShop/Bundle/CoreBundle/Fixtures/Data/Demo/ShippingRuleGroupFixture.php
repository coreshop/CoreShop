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
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShippingRuleGroupFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
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
            ShippingRuleFixture::class,
            CarrierFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->container->get('coreshop.repository.shipping_rule_group')->findAll())) {
            $carrier = $this->getReference('carrier');

            /**
             * @var ShippingRuleGroupInterface $shippingRuleGroup
             */
            $shippingRuleGroup = $this->container->get('coreshop.factory.shipping_rule_group')->createNew();
            $shippingRuleGroup->setShippingRule($this->getReference('shippingRule0'));
            $shippingRuleGroup->setPriority(1);
            $shippingRuleGroup->setCarrier($carrier);

            $shippingRuleGroup2 = $this->container->get('coreshop.factory.shipping_rule_group')->createNew();
            $shippingRuleGroup2->setShippingRule($this->getReference('shippingRule1'));
            $shippingRuleGroup2->setPriority(1);
            $shippingRuleGroup2->setCarrier($carrier);

            $shippingRuleGroup3 = $this->container->get('coreshop.factory.shipping_rule_group')->createNew();
            $shippingRuleGroup3->setShippingRule($this->getReference('shippingRule2'));
            $shippingRuleGroup3->setPriority(1);
            $shippingRuleGroup3->setCarrier($carrier);

            $manager->persist($shippingRuleGroup);
            $manager->persist($shippingRuleGroup2);
            $manager->persist($shippingRuleGroup3);
            $manager->flush();
        }
    }
}
