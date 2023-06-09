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

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Component\Taxation\Repository\TaxRateRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TaxRateFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private TaxRateRepositoryInterface $taxRateRepository,
        private FactoryInterface $taxRateFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['demo'];
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->taxRateRepository->findAll())) {
            /**
             * @var TaxRateInterface $taxRate
             */
            $taxRate = $this->taxRateFactory->createNew();
            $taxRate->setName('20AT', 'de');
            $taxRate->setName('20AT', 'en');
            $taxRate->setActive(true);
            $taxRate->setRate(20);

            $taxRate10 = $this->taxRateFactory->createNew();
            $taxRate10->setName('10AT', 'de');
            $taxRate10->setName('10AT', 'en');
            $taxRate10->setActive(true);
            $taxRate10->setRate(10);

            $manager->persist($taxRate);
            $manager->persist($taxRate10);
            $manager->flush();

            $this->addReference('taxRate', $taxRate);
            $this->addReference('taxRate10', $taxRate10);
        }
    }
}
