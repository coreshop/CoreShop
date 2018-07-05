<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180226085136 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->connection->executeQuery('CREATE TABLE coreshop_store_countries (store_id INT NOT NULL, country_id INT NOT NULL, INDEX IDX_9F906C47B092A811 (store_id), INDEX IDX_9F906C47F92F3E70 (country_id), PRIMARY KEY(store_id, country_id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
        $this->connection->executeQuery('ALTER TABLE coreshop_store_countries ADD CONSTRAINT FK_9F906C47B092A811 FOREIGN KEY (store_id) REFERENCES coreshop_store (id);');
        $this->connection->executeQuery('ALTER TABLE coreshop_store_countries ADD CONSTRAINT FK_9F906C47F92F3E70 FOREIGN KEY (country_id) REFERENCES coreshop_country (id);');

        $this->connection->executeQuery('INSERT INTO coreshop_store_countries SELECT store_id, country_id FROM coreshop_country_stores;');
        $this->connection->executeQuery('ALTER TABLE coreshop_country_stores RENAME _DEPRECATED_coreshop_country_stores;');

        $this->connection->executeQuery('TRUNCATE TABLE cache;');
        $this->connection->executeQuery('TRUNCATE TABLE cache_tags;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
