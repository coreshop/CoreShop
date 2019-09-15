<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\BigIntType;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190617082805 extends AbstractPimcoreMigration
{
    public function up(Schema $schema)
    {
        if ($schema->hasTable('coreshop_product_store_values')) {
            $table = $schema->getTable('coreshop_product_store_values');
            if ($table->hasColumn('price')) {
                if (!$table->getColumn('price')->getType() instanceof BigIntType) {
                    $this->addSql('ALTER TABLE coreshop_product_store_values CHANGE price price BIGINT NOT NULL;');
                }
            }
        }

        if ($schema->hasTable('coreshop_product_unit_definition_price')) {
            $table = $schema->getTable('coreshop_product_unit_definition_price');
            if ($table->hasColumn('price')) {
                if (!$table->getColumn('price')->getType() instanceof BigIntType) {
                    $this->addSql('ALTER TABLE coreshop_product_unit_definition_price CHANGE price price BIGINT NOT NULL;');
                }
            }
        }

        if ($schema->hasTable('coreshop_product_quantity_price_rule_range')) {
            $table = $schema->getTable('coreshop_product_quantity_price_rule_range');
            if ($table->hasColumn('amount')) {
                if (!$table->getColumn('amount')->getType() instanceof BigIntType) {
                    $this->addSql('ALTER TABLE coreshop_product_quantity_price_rule_range CHANGE amount amount BIGINT NOT NULL;');
                }
            }

            if ($table->hasColumn('pseudo_price')) {
                if (!$table->getColumn('pseudo_price')->getType() instanceof BigIntType) {
                    $this->addSql('ALTER TABLE coreshop_product_quantity_price_rule_range CHANGE pseudo_price pseudo_price BIGINT NOT NULL;');
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
