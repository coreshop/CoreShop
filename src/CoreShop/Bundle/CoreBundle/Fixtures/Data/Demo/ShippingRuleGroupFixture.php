<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShippingRuleGroupFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return [
            ShippingRuleFixture::class,
            CarrierFixture::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
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
