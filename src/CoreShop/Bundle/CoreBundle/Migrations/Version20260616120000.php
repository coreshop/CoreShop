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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Adds the CoreShopOrder.lastActivatedAt field used to restore the last-opened
 * cart across sessions.
 */
final class Version20260616120000 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return 'Add CoreShopOrder.lastActivatedAt for last-opened cart persistence';
    }

    public function up(Schema $schema): void
    {
        $classUpdater = new ClassUpdate(
            $this->container->getParameter('coreshop.model.order.pimcore_class_name'),
        );

        if ($classUpdater->hasField('lastActivatedAt')) {
            return;
        }

        $classUpdater->insertFieldAfter('orderDate', [
            'name' => 'lastActivatedAt',
            'title' => 'Last Activated At',
            'tooltip' => 'Timestamp the cart was last selected/activated by its customer; used to restore the last-opened cart across sessions.',
            'mandatory' => false,
            'noteditable' => true,
            'index' => true,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'fieldtype' => 'datetime',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
            'defaultValue' => null,
            'useCurrentDate' => false,
            'queryColumnType' => 'bigint(20)',
            'columnType' => 'bigint(20)',
            'phpdocType' => '\\Carbon\\Carbon',
            'datatype' => 'data',
        ]);

        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        $classUpdater = new ClassUpdate(
            $this->container->getParameter('coreshop.model.order.pimcore_class_name'),
        );

        if (!$classUpdater->hasField('lastActivatedAt')) {
            return;
        }

        $classUpdater->removeField('lastActivatedAt');
        $classUpdater->save();
    }
}
