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
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class StoreFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
        private FactoryInterface $storeFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['application'];
    }

    public function load(ObjectManager $manager): void
    {
        if (!$this->storeRepository->findStandard()) {
            $store = $this->storeFactory->createNew();
            $store->setName('Standard');
            $store->setTemplate('Standard');
            $store->setIsDefault(true);
            $store->setUseGrossPrice(false);

            $manager->persist($store);
            $manager->flush();

            $this->setReference('store', $store);
        } else {
            $this->setReference('store', $this->storeRepository->findStandard());
        }
    }
}
