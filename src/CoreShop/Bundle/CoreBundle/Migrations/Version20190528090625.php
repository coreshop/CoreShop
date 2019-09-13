<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

class Version20190528090625 extends AbstractPimcoreMigration
{
    public function up(Schema $schema)
    {
        if ($schema->hasTable('coreshop_product_quantity_price_rule_range')) {
            $this->addSql(
                'ALTER TABLE `coreshop_product_quantity_price_rule_range`
                     CHANGE `range_from` `range_starting_from` int(11) NOT NULL AFTER `unit_definition`,
                     DROP `range_to`;'
            );
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
