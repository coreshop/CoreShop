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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Bundle\PayumBundle\Model\GatewayConfig;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Lorem;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PaymentProviderFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if (!count($this->container->get('coreshop.repository.payment_provider')->findAll())) {
            $defaultStore = $this->container->get('coreshop.repository.store')->findStandard();

            $faker = Factory::create();
            $faker->addProvider(new Lorem($faker));

            /**
             * @var $provider PaymentProviderInterface
             */
            $provider = $this->container->get('coreshop.factory.payment_provider')->createNew();

            $gatewayConfig = new GatewayConfig();
            $gatewayConfig->setFactoryName('offline');
            $gatewayConfig->setGatewayName('offline');

            $provider->setIdentifier('Bankwire');
            $provider->setActive(true);
            $provider->setPosition(1);
            $provider->addStore($defaultStore);
            $provider->setGatewayConfig($gatewayConfig);

            foreach (Tool::getValidLanguages() as $lang) {
                $provider->setName('Bankwire', $lang);
                $provider->setDescription(implode(PHP_EOL, $faker->paragraphs(3)), $lang);
                $provider->setInstructions(implode(PHP_EOL, $faker->paragraphs(3)), $lang);
            }

            $manager->persist($provider);
            $manager->flush();

            $this->setReference('payment_provider', $provider);
        }
    }
}
