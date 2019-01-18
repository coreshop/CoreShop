<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190114155520 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('coreshop_product_tier_price')) {
            $this->addSql('CREATE TABLE coreshop_product_tier_price (id INT AUTO_INCREMENT NOT NULL, store_id INT DEFAULT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, productId INT NOT NULL COMMENT \'(DC2Type:pimcoreObject)\', property VARCHAR(255) NOT NULL, INDEX IDX_FE41025EB092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('CREATE TABLE coreshop_product_tier_price_range (id INT AUTO_INCREMENT NOT NULL, tier_price_id INT DEFAULT NULL, range_from INT NOT NULL, range_to INT NOT NULL, price INT NOT NULL, percentage_discount DOUBLE PRECISION NOT NULL, highlighted TINYINT(1) NOT NULL, INDEX IDX_C909EDB0D20FFFF (tier_price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('ALTER TABLE coreshop_product_tier_price ADD CONSTRAINT FK_FE41025EB092A811 FOREIGN KEY (store_id) REFERENCES coreshop_store (id) ON DELETE SET NULL;');
            $this->addSql('ALTER TABLE coreshop_product_tier_price_range ADD CONSTRAINT FK_C909EDB0D20FFFF FOREIGN KEY (tier_price_id) REFERENCES coreshop_product_tier_price (id);');
        }
    }

    public function postUp(Schema $schema)
    {
        //Migrate values from product class to new table.
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
