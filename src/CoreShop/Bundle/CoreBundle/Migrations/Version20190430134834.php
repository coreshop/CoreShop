<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190430134834 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($schema->hasTable('coreshop_product_store_price')) {
            $this->addSql('ALTER TABLE coreshop_product_store_price CHANGE property property VARCHAR(190) NOT NULL;');
            $this->addSql('CREATE INDEX IDX_514E3EBF367996058BF21CDE ON coreshop_product_store_price (productId, property);');
            $this->addSql('CREATE INDEX IDX_514E3EBF367996052F738A528BF21CDE ON coreshop_product_store_price (productId, storeId, property);');
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
