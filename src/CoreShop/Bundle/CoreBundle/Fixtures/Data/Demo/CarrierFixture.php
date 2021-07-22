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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Lorem;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CarrierFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
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

    public function getDependencies(): array
    {
        return [
            TaxRuleGroupFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->container->get('coreshop.repository.carrier')->findAll())) {
            $defaultStore = $this->container->get('coreshop.repository.store')->findStandard();

            $faker = Factory::create();
            $faker->addProvider(new Lorem($faker));

            /**
             * @var CarrierInterface $carrier
             */
            $carrier = $this->container->get('coreshop.factory.carrier')->createNew();
            $carrier->setIdentifier('Standard');
            $carrier->setTrackingUrl('https://coreshop.at/track/%s');
            $carrier->setHideFromCheckout(false);
            $carrier->setIsFree(false);
            $carrier->setTaxRule($this->getReference('taxRule'));
            $carrier->addStore($defaultStore);

            foreach (Tool::getValidLanguages() as $lang) {
                $carrier->setDescription(implode(PHP_EOL, $faker->paragraphs(3)), $lang);
                $carrier->setTitle('Standard - ' . strtoupper($lang), $lang);
            }

            $manager->persist($carrier);
            $manager->flush();

            $this->setReference('carrier', $carrier);
        }
    }
}
