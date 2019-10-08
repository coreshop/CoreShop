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

        if ($schema->hasTable('coreshop_payment_provider')) {

            $table = $schema->getTable('coreshop_payment_provider');

            if ($table->hasColumn('logo')) {
                $table->dropColumn('logo');
            }

            if ($table->hasColumn('logoId')) {
                $table->dropColumn('logoId');
            }

            $table->addColumn('logo', 'integer', [
                'length' => 11,
                'comment' => '(DC2Type:pimcoreAsset)',
                'notnull' => false
            ]);

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
