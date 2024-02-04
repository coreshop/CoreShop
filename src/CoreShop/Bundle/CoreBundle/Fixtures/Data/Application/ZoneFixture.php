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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Application;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;

class ZoneFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private FactoryInterface $zoneFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['application'];
    }

    public function load(ObjectManager $manager): void
    {
        $countries = CountryLoader::countries(true, true);
        $continents = [];

        foreach ($countries as $country) {
            if ($country instanceof Country) {
                $continent = $country->getContinent();

                if (!in_array($continent, $continents)) {
                    $continents[] = $continent;
                }
            }
        }

        foreach ($continents as $continent) {
            $zone = $this->zoneFactory->createNew();
            $zone->setName($continent);
            $zone->setActive(true);

            $manager->persist($zone);
        }

        $manager->flush();
    }
}
