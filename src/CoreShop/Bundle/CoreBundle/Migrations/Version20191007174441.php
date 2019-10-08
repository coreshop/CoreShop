<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

class Version20191007174441 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        if ($schema->hasTable('coreshop_payment_provider')) {
            $this->addSql('ALTER TABLE coreshop_payment_provider CHANGE logoId logoId INT DEFAULT NULL COMMENT `(DC2Type:pimcoreAsset)`');
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
