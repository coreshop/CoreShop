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

namespace CoreShop\Bundle\FixtureBundle\Fixture\Loader;

use CoreShop\Bundle\FixtureBundle\Fixture\LoadedFixtureVersionAwareInterface;
use CoreShop\Bundle\FixtureBundle\Fixture\Sorter\DataFixturesSorter;
use CoreShop\Bundle\FixtureBundle\Fixture\UpdateDataFixturesFixture;
use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Bundle\FixtureBundle\Model\DataFixtureInterface;
use CoreShop\Bundle\FixtureBundle\Repository\DataFixtureRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataFixturesLoader extends ContainerAwareLoader
{
    protected array $loadedFixtures = [];

    protected ?\ReflectionProperty $ref = null;

    public function __construct(
        protected EntityManager $em,
        ContainerInterface $container,
        protected UpdateDataFixturesFixture $updateDataFixturesFixture,
        protected DataFixtureRepositoryInterface $dataFixtureRepository
    ) {
        parent::__construct($container);
    }

    public function getFixtures()
    {
        $sorter = new DataFixturesSorter();
        $fixtures = $sorter->sort($this->getAllFixtures());

        // remove already loaded fixtures
        foreach ($fixtures as $key => $fixture) {
            if ($this->isFixtureAlreadyLoaded($fixture)) {
                unset($fixtures[$key]);
            }
        }

        // add a special fixture to mark new fixtures as "loaded"
        if (!empty($fixtures)) {
            $toBeLoadFixtureClassNames = [];
            foreach ($fixtures as $fixture) {
                $version = null;
                if ($fixture instanceof VersionedFixtureInterface) {
                    $version = $fixture->getVersion();
                }
                $toBeLoadFixtureClassNames[$fixture::class] = $version;
            }

            $updateFixture = $this->updateDataFixturesFixture;
            $updateFixture->setDataFixtures($toBeLoadFixtureClassNames);
            $fixtures[$updateFixture::class] = $updateFixture;
        }

        return $fixtures;
    }

    /**
     * Determines whether the given data fixture is already loaded or not.
     *
     * @param object $fixtureObject
     *
     * @return bool
     */
    protected function isFixtureAlreadyLoaded($fixtureObject)
    {
        if (count($this->loadedFixtures) === 0) {
            $this->loadedFixtures = [];

            $loadedFixtures = $this->dataFixtureRepository->findAll();
            /** @var DataFixtureInterface $fixture */
            foreach ($loadedFixtures as $fixture) {
                $this->loadedFixtures[$fixture->getClassName()] = $fixture->getVersion() ?: '0.0';
            }
        }

        $alreadyLoaded = false;

        if (isset($this->loadedFixtures[$fixtureObject::class])) {
            $alreadyLoaded = true;
            $loadedVersion = $this->loadedFixtures[$fixtureObject::class];
            if ($fixtureObject instanceof VersionedFixtureInterface
                && version_compare($loadedVersion, $fixtureObject->getVersion()) == -1
            ) {
                if ($fixtureObject instanceof LoadedFixtureVersionAwareInterface) {
                    $fixtureObject->setLoadedVersion($loadedVersion);
                }
                $alreadyLoaded = false;
            }
        }

        return $alreadyLoaded;
    }

    /**
     * @return array
     */
    protected function getAllFixtures()
    {
        if (!$this->ref) {
            $this->ref = new \ReflectionProperty('Doctrine\Common\DataFixtures\Loader', 'fixtures');
            $this->ref->setAccessible(true);
        }

        return $this->ref->getValue($this);
    }
}
