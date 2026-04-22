<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Carbon\Carbon;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore;

final class Version20260419121310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add passwordResetHashCreatedAt datetime field to the User class (required by UserInterface::getPasswordResetHashCreatedAt introduced in bb2de14712).';
    }

    public function up(Schema $schema): void
    {
        $classUpdater = new ClassUpdate(
            Pimcore::getContainer()->getParameter('coreshop.model.user.pimcore_class_name'),
        );

        if ($classUpdater->hasField('passwordResetHashCreatedAt')) {
            return;
        }

        $passwordResetHashCreatedAtField = [
            'fieldtype' => 'datetime',
            'queryColumnType' => 'bigint',
            'columnType' => 'bigint',
            'phpdocType' => Carbon::class,
            'defaultValue' => null,
            'useCurrentDate' => false,
            'name' => 'passwordResetHashCreatedAt',
            'title' => 'Reset Password Hash Created At',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => true,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $classUpdater->insertFieldAfter('passwordResetHash', $passwordResetHashCreatedAtField);
        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
