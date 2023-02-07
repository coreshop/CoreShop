<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230127064907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $doMigrate = false;
        $foreignKeys = $schema->getTable('coreshop_carrier_stores')->getForeignKeys();

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getLocalColumns()[0] === 'carrier_id' && $foreignKey->getForeignTableName() === 'coreshop_store') {
                $doMigrate = true;
                break;
            }
        }

        if ($doMigrate) {
            $table = $schema->getTable('coreshop_carrier_stores');
            $this->addSql(
                'ALTER TABLE coreshop_carrier_stores RENAME COLUMN store_id TO carrier_id, RENAME COLUMN carrier_id to store_id;'
            );

            foreach ($foreignKeys as $foreignKey) {
                $table->removeForeignKey($foreignKey->getName());
            }

            $indices = $table->getIndexes();

            foreach ($indices as $index) {
                if ($index->getName() === 'PRIMARY') {
                    continue;
                }
                $table->dropIndex($index->getName());
            }

            $table->addForeignKeyConstraint(
                'coreshop_store',
                ['store_id'],
                ['id'],
                [],
                'FK_E7EE2F7CB092A811'
            );
            $table->addForeignKeyConstraint(
                'coreshop_carrier',
                ['carrier_id'],
                ['id'],
                [],
                'FK_E7EE2F7C21DFC797'
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
