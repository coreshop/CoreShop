<?php

namespace CoreShop\Bundle\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Okvpn\Bundle\MigrationBundle\Entity\DataFixture;
use Okvpn\Bundle\MigrationBundle\Entity\DataMigration;

class OkvpnEntityResolverListener implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /**
         * @var $metadata ClassMetadataInfo
         */
        $metadata = $eventArgs->getClassMetadata();
        $table = $metadata->table;

        if ($metadata->getName() === DataMigration::class) {
            $table['name'] = 'coreshop_migrations';
            $metadata->setPrimaryTable($table);
        }

        if ($metadata->getName() === DataFixture::class) {
            $table['name'] = 'coreshop_migrations_fixtures';
            $metadata->setPrimaryTable($table);
        }
    }
}