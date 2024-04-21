<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\User\Permission\Definition;

final class Version20240421093551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $permission = Definition::getByKey('coreshop_permission_messenger');

        if (null === $permission) {
            $permission = new Definition();
            $permission->setKey('coreshop_permission_messenger');
            $permission->setCategory('coreshop_permission_group_coreshop');
            $permission->save();
        }
    }

    public function down(Schema $schema): void
    {

    }
}
