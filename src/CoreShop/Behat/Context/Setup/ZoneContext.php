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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Persistence\ObjectManager;

final class ZoneContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ObjectManager $objectManager;
    private FactoryInterface $zoneFactory;
    private RepositoryInterface $zoneRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $zoneFactory,
        RepositoryInterface $zoneRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->zoneFactory = $zoneFactory;
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * @Given /^the site has a zone "([^"]+)"$/
     */
    public function theSiteHasAZone($name): void
    {
        $this->createZone($name);
    }

    /**
     * @param string $name
     */
    private function createZone($name): void
    {
        $zone = $this->zoneRepository->findBy(['name' => $name]);

        if (!$zone) {
            /**
             * @var ZoneInterface $zone
             */
            $zone = $this->zoneFactory->createNew();
            $zone->setName($name);

            $this->saveZone($zone);
        }
    }

    /**
     * @param ZoneInterface $zone
     */
    private function saveZone(ZoneInterface $zone): void
    {
        $this->objectManager->persist($zone);
        $this->objectManager->flush();

        $this->sharedStorage->set('zone', $zone);
    }
}
