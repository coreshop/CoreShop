<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\BigIntType;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

class Version20200224164328 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(' ALTER TABLE coreshop_payment DROP client_email, DROP client_id;');

        if ($schema->hasTable('coreshop_payment')) {
            $table = $schema->getTable('coreshop_payment');
            if ($table->hasColumn('total_amount')) {
                if (!$table->getColumn('total_amount')->getType() instanceof BigIntType) {
                    $this->addSql('ALTER TABLE coreshop_payment CHANGE total_amount total_amount BIGINT NOT NULL;');
                }
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
