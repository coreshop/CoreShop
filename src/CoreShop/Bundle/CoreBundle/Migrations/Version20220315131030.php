<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220315131030 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if ($schema->getTable('coreshop_carrier')->hasColumn('isFree')) {
            $this->addSql('ALTER TABLE coreshop_carrier DROP COLUMN `isFree`');
        }
    }

    public function down(Schema $schema): void
    {
        // do nothing due to potential data loss
    }
}
