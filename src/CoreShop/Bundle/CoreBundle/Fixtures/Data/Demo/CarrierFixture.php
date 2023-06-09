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

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Lorem;
use Pimcore\Tool;

class CarrierFixture extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{

    public function __construct(
        private CarrierRepositoryInterface $carrierRepository,
        private StoreRepositoryInterface $storeRepository,
        private FactoryInterface $carrierFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['demo'];
    }

    public function getDependencies(): array
    {
        return [
            TaxRuleGroupFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->carrierRepository->findAll())) {
            $defaultStore = $this->storeRepository->findStandard();

            $faker = Factory::create();
            $faker->addProvider(new Lorem($faker));

            /**
             * @var CarrierInterface $carrier
             */
            $carrier = $this->carrierFactory->createNew();
            $carrier->setIdentifier('Standard');
            $carrier->setTrackingUrl('https://coreshop.at/track/%s');
            $carrier->setHideFromCheckout(false);
            $carrier->setTaxRule($this->getReference('taxRule'));
            $carrier->addStore($defaultStore);

            foreach (Tool::getValidLanguages() as $lang) {
                $carrier->setDescription(implode(\PHP_EOL, $faker->paragraphs(3)), $lang);
                $carrier->setTitle('Standard - '.strtoupper($lang), $lang);
            }

            $manager->persist($carrier);
            $manager->flush();

            $this->setReference('carrier', $carrier);
        }
    }
}
