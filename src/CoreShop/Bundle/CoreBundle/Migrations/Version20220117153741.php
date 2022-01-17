<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220117153741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->getTable('coreshop_payment')->hasColumn('details')) {
            $this->addSql("ALTER TABLE coreshop_payment ADD details LONGTEXT NOT NULL COMMENT '(DC2Type:json_array)';");
        }
    }

    public function down(Schema $schema): void
    {

    }
}
