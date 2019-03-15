<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190315113012 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE coreshop_product_unit_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, full_label VARCHAR(255) NOT NULL, full_plural_label VARCHAR(255) NOT NULL, short_label VARCHAR(255) NOT NULL, short_plural_label VARCHAR(255) NOT NULL, creationDate DATE NOT NULL, modificationDate DATETIME DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_7A572A8D2C2AC5D3 (translatable_id), UNIQUE INDEX coreshop_product_unit_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
            ALTER TABLE coreshop_product_unit_translation ADD CONSTRAINT FK_7A572A8D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES coreshop_product_unit (id) ON DELETE CASCADE;
            ALTER TABLE coreshop_product_unit ADD creationDate DATE NOT NULL, ADD modificationDate DATETIME DEFAULT NULL;
        ');

        $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
