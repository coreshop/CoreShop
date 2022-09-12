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

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddressIdentifierFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
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
            $addressIdentifier = $this->container->get('coreshop.factory.address_identifier')->createNew();
            $addressIdentifier->setName($entry['name']);
            $addressIdentifier->setActive(true);
            $manager->persist($addressIdentifier);
        }

        $manager->flush();
    }
}
