<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

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
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /**
         * @var ClassMetadataInfo
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
