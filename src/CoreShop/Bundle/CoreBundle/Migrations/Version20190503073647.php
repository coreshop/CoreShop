<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190503073647 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE coreshop_cart_price_rule_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, `label` VARCHAR(255) DEFAULT NULL, creationDate DATE NOT NULL, modificationDate DATETIME DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_3A3D1D4B2C2AC5D3 (translatable_id), UNIQUE INDEX coreshop_cart_price_rule_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
            ALTER TABLE coreshop_cart_price_rule_translation ADD CONSTRAINT FK_3A3D1D4B2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES coreshop_cart_price_rule (id) ON DELETE CASCADE;
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
