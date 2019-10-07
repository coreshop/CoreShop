<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20191007174441 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        // check if logoId column is already in the table
        if ($schema->hasTable('coreshop_payment_provider')) {

            $table = $schema->getTable('coreshop_payment_provider');

            if (!$table->hasColumn('logoId')) {
                $this->addSql('
                    ALTER TABLE coreshop_payment_provider ADD COLUMN logoId int(11) DEFAULT NULL AFTER position
                ');
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
