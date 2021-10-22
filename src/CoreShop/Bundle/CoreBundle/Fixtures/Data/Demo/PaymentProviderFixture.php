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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\PayumPayment\Model\GatewayConfig;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Lorem;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PaymentProviderFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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

    public function load(ObjectManager $manager): void
    {
        if (!count($this->container->get('coreshop.repository.payment_provider')->findAll())) {
            $defaultStore = $this->container->get('coreshop.repository.store')->findStandard();

            $faker = Factory::create();
            $faker->addProvider(new Lorem($faker));

            /**
             * @var PaymentProviderInterface $provider
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
                $provider->setTitle('Bankwire', $lang);
                $provider->setDescription(implode(\PHP_EOL, $faker->paragraphs(3)), $lang);
                $provider->setInstructions(implode(\PHP_EOL, $faker->paragraphs(3)), $lang);
            }

            $manager->persist($provider);
            $manager->flush();

            $this->setReference('payment_provider', $provider);
        }
    }
}
