<?php

namespace CoreShop\Bundle\CoreBundle\Migrations\Data\ORM;

use CoreShop\Component\Address\Model\ZoneInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Okvpn\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ZoneFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
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
            /**
             * @var $zone ZoneInterface
             */
            $zone = $this->container->get('coreshop.factory.zone')->createNew();
            $zone->setName($continent);
            $zone->setActive(true);

            $manager->persist($zone);
        }

        $manager->flush();
    }
}