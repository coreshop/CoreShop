<?php

namespace CoreShop\Bundle\FixtureBundle\Fixture;

use CoreShop\Bundle\FixtureBundle\Repository\DataFixtureRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class UpdateDataFixturesFixture extends AbstractFixture
{
    /**
     * @var FactoryInterface
     */
    protected $fixtureFactory;

    /**
     * @var DataFixtureRepositoryInterface
     */
    protected $fixtureRepository;

    /**
     * @var array
     *            key - class name
     *            value - current loaded version
     */
    protected $dataFixturesClassNames;

    /**
     * @param FactoryInterface $fixtureFactory
     */
    public function __construct(FactoryInterface $fixtureFactory, DataFixtureRepositoryInterface $fixtureRepository)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->fixtureRepository = $fixtureRepository;
    }

    /**
     * Set a list of data fixtures to be updated.
     *
     * @param array $classNames
     */
    public function setDataFixtures($classNames)
    {
        $this->dataFixturesClassNames = $classNames;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
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
