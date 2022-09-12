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

namespace CoreShop\Bundle\FixtureBundle\Fixture;

use CoreShop\Bundle\FixtureBundle\Repository\DataFixtureRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class UpdateDataFixturesFixture extends AbstractFixture
{
    protected array $dataFixturesClassNames;

    public function __construct(
        protected FactoryInterface $fixtureFactory,
        protected DataFixtureRepositoryInterface $fixtureRepository,
    ) {
    }

    /**
     * Set a list of data fixtures to be updated.
     */
    public function setDataFixtures(array $classNames): void
    {
        $this->dataFixturesClassNames = $classNames;
    }

    public function load(ObjectManager $manager): void
    {
        if (!empty($this->dataFixturesClassNames)) {
            $loadedAt = new \DateTime('now', new \DateTimeZone('UTC'));
            foreach ($this->dataFixturesClassNames as $className => $version) {
                $dataFixture = null;
                if ($version !== null) {
                    $dataFixture = $this->fixtureRepository->findOneBy(['className' => $className]);
                }
                if (!$dataFixture) {
                    $dataFixture = $this->fixtureFactory->createNew();
                    $dataFixture->setClassName($className);
                }

                $dataFixture->setVersion($version);
                $dataFixture->setLoadedAt($loadedAt);

                $manager->persist($dataFixture);
            }
            $manager->flush();
        }
    }
}
