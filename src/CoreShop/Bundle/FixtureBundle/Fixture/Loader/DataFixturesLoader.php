<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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
    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $loadedFixtures;

    /** @var \ReflectionProperty */
    protected $ref;

    /**
     * @var UpdateDataFixturesFixture
     */
    protected $updateDataFixturesFixture;

    /**
     * @var DataFixtureRepositoryInterface
     */
    protected $dataFixtureRepository;

    /**
     * @param EntityManager                  $em
     * @param ContainerInterface             $container
     * @param UpdateDataFixturesFixture      $updateDataFixturesFixture
     * @param DataFixtureRepositoryInterface $dataFixtureRepository
     */
    public function __construct(
        EntityManager $em,
        ContainerInterface $container,
        UpdateDataFixturesFixture $updateDataFixturesFixture,
        DataFixtureRepositoryInterface $dataFixtureRepository
    ) {
        parent::__construct($container);

        $this->em = $em;
        $this->updateDataFixturesFixture = $updateDataFixturesFixture;
        $this->dataFixtureRepository = $dataFixtureRepository;
    }

    /**
     * {@inheritdoc}
     */
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
                $toBeLoadFixtureClassNames[get_class($fixture)] = $version;
            }

            $updateFixture = $this->updateDataFixturesFixture;
            $updateFixture->setDataFixtures($toBeLoadFixtureClassNames);
            $fixtures[get_class($updateFixture)] = $updateFixture;
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
        if (!is_array($this->loadedFixtures) || count($this->loadedFixtures) === 0) {
            $this->loadedFixtures = [];

            $loadedFixtures = $this->dataFixtureRepository->findAll();
            /** @var DataFixtureInterface $fixture */
            foreach ($loadedFixtures as $fixture) {
                $this->loadedFixtures[$fixture->getClassName()] = $fixture->getVersion() ?: '0.0';
            }
        }

        $alreadyLoaded = false;

        if (isset($this->loadedFixtures[get_class($fixtureObject)])) {
            $alreadyLoaded = true;
            $loadedVersion = $this->loadedFixtures[get_class($fixtureObject)];
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
