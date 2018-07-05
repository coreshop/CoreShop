<?php

namespace CoreShop\Bundle\FixtureBundle\Fixture;

use CoreShop\Bundle\FixtureBundle\Entity\DataFixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class UpdateDataFixturesFixture extends AbstractFixture
{
    /**
     * @var array
     *            key - class name
     *            value - current loaded version
     */
    protected $dataFixturesClassNames;

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
                if (null !== $version) {
                    $dataFixture = $manager
                        ->getRepository('CoreShopFixtureBundle:DataFixture')
                        ->findOneBy(['className' => $className]);
                }
                if (!$dataFixture) {
                    $dataFixture = new DataFixture();
                    $dataFixture->setClassName($className);
                }

                $dataFixture
                    ->setVersion($version)
                    ->setLoadedAt($loadedAt);
                $manager->persist($dataFixture);
            }
            $manager->flush();
        }
    }
}
