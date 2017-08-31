<?php

namespace CoreShop\Bundle\AdminBundle\Migrations;

use CoreShop\Component\Address\Model\CountryInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migrates Country AddressFormat from %Object to %DataObject
 */
class Version20170831140539 extends AbstractPimcoreMigration
{
    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return \Pimcore::getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema)
    {
        $this->run("Object", "DataObject");
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema)
    {
        $this->run("DataObject", "Object");
    }

    /**
     * Runs the actual migration
     *
     * @param $from
     * @param $to
     */
    private function run($from, $to) {
        // this up() migration is auto-generated, please modify it to your needs
        $repository = $this->getContainer()->get('coreshop.repository.country');
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $countries = $repository->getAll();

        /**
         * @var $country CountryInterface
         */
        foreach ($countries as $country) {
            $country->setAddressFormat(str_replace("%" . $from, "%" . $to, $country->getAddressFormat()));

            $entityManager->persist($country);
        }

        if (!$this->isDryRun()) {
            $entityManager->flush();
        }
    }
}
