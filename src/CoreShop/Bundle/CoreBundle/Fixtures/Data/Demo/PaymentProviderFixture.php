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

use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\PayumPayment\Model\GatewayConfig;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Lorem;
use Pimcore\Tool;

class PaymentProviderFixture extends Fixture implements FixtureGroupInterface
{


    public function __construct(
        private PaymentProviderRepositoryInterface $paymentProviderRepository,
        private StoreRepositoryInterface $storeRepository,
        private FactoryInterface $paymentProviderFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['demo'];
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->paymentProviderRepository->findAll())) {
            $defaultStore = $this->storeRepository->findStandard();

            $faker = Factory::create();
            $faker->addProvider(new Lorem($faker));

            /**
             * @var PaymentProviderInterface $provider
             */
            $provider = $this->paymentProviderFactory->createNew();

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
