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
            $this->addSql('CREATE TABLE coreshop_product_tier_price_range (id INT AUTO_INCREMENT NOT NULL, range_from INT NOT NULL, range_to INT NOT NULL, pricing_behaviour VARCHAR(255) NOT NULL, amount INT NOT NULL, percentage DOUBLE PRECISION NOT NULL, pseudo_price INT NOT NULL, highlighted TINYINT(1) NOT NULL, currencyId INT DEFAULT NULL, INDEX IDX_C909EDB091000B8A (currencyId), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('CREATE TABLE coreshop_product_specific_tier_price_rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, priority INT NOT NULL, product INT NOT NULL, creationDate DATETIME NOT NULL, modificationDate DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('CREATE TABLE coreshop_product_specific_tier_price_rule_conditions (tier_price_rule_id INT NOT NULL, condition_id INT NOT NULL, INDEX IDX_677BA31C3BC99698 (tier_price_rule_id), INDEX IDX_677BA31C887793B6 (condition_id), PRIMARY KEY(tier_price_rule_id, condition_id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('CREATE TABLE coreshop_product_specific_tier_price_rule_ranges (tier_price_rule_id INT NOT NULL, range_id INT NOT NULL, INDEX IDX_8BE4180B3BC99698 (tier_price_rule_id), INDEX IDX_8BE4180B2A82D0B1 (range_id), PRIMARY KEY(tier_price_rule_id, range_id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('ALTER TABLE coreshop_product_tier_price_range ADD CONSTRAINT FK_C909EDB091000B8A FOREIGN KEY (currencyId) REFERENCES coreshop_currency (id) ON DELETE SET NULL;');
            $this->addSql('ALTER TABLE coreshop_product_specific_tier_price_rule_conditions ADD CONSTRAINT FK_677BA31C3BC99698 FOREIGN KEY (tier_price_rule_id) REFERENCES coreshop_product_specific_tier_price_rule (id) ON DELETE CASCADE;');
            $this->addSql('ALTER TABLE coreshop_product_specific_tier_price_rule_conditions ADD CONSTRAINT FK_677BA31C887793B6 FOREIGN KEY (condition_id) REFERENCES coreshop_rule_condition (id) ON DELETE CASCADE;');
            $this->addSql('ALTER TABLE coreshop_product_specific_tier_price_rule_ranges ADD CONSTRAINT FK_8BE4180B3BC99698 FOREIGN KEY (tier_price_rule_id) REFERENCES coreshop_product_specific_tier_price_rule (id) ON DELETE CASCADE;');
            $this->addSql('ALTER TABLE coreshop_product_specific_tier_price_rule_ranges ADD CONSTRAINT FK_8BE4180B2A82D0B1 FOREIGN KEY (range_id) REFERENCES coreshop_product_tier_price_range (id) ON DELETE CASCADE;');
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
