<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

class Version20190527090625 extends AbstractPimcoreMigration
{
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('coreshop_product_quantity_price_rule_range')) {
            return;
        }

        $table = $schema->getTable('coreshop_product_quantity_price_rule_range');

        if ($table->hasColumn('unit_definition')) {
            return;
        }

        $this->addSql(
            'ALTER TABLE coreshop_product_quantity_price_rule_range ADD unit_definition INT DEFAULT NULL;
             ALTER TABLE coreshop_product_quantity_price_rule_range ADD CONSTRAINT FK_C6BA05DA6B98B918 FOREIGN KEY (unit_definition) REFERENCES coreshop_product_unit_definition (id);
             CREATE INDEX IDX_C6BA05DA6B98B918 ON coreshop_product_quantity_price_rule_range (unit_definition);'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
