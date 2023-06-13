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

use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AddressIdentifierFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(private FactoryInterface $addressIdentifierFactory)
    {
    }

    public static function getGroups(): array
    {
        return ['application'];
    }

    public function load(ObjectManager $manager): void
    {
        $fixtureData = [
            1 => [
                'name' => 'shipping',
            ],
            2 => [
                'name' => 'invoice',
            ],
        ];

        foreach ($fixtureData as $entry) {
            /**
             * @var AddressIdentifierInterface $addressIdentifier
             */
            $addressIdentifier = $this->addressIdentifierFactory->createNew();
            $addressIdentifier->setName($entry['name']);
            $addressIdentifier->setActive(true);
            $manager->persist($addressIdentifier);
        }

        $manager->flush();
    }
}
