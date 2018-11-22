<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180409073455 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('coreshop_product_store_price')) {
            $this->addSql('CREATE TABLE coreshop_product_store_price (id INT AUTO_INCREMENT NOT NULL, productId INT NOT NULL COMMENT \'(DC2Type:pimcoreObject)\', price INT NOT NULL, storeId INT DEFAULT NULL, INDEX IDX_514E3EBF2F738A52 (storeId), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
            $this->addSql('ALTER TABLE coreshop_product_store_price ADD CONSTRAINT FK_514E3EBF2F738A52 FOREIGN KEY (storeId) REFERENCES coreshop_store (id) ON DELETE SET NULL;');

            $tableName = sprintf('object_store_%s', $this->container->get('coreshop.repository.product')->getClassId());
            $products = $this->connection->fetchAll(sprintf('SELECT * FROM %s', $tableName));

            foreach ($products as $pr) {
                $id = $pr['oo_id'];
                $storePricesSerialized = $pr['storePrice'];
                $storePrices = unserialize($storePricesSerialized);

                if (is_array($storePrices)) {
                    foreach ($storePrices as $storeId => $storePrice) {
                        $this->addSql(sprintf('INSERT INTO coreshop_product_store_price (productId, storeId, price) VALUES (%s, %s, %s)', $id, $storeId, $storePrice));
                    }
                }
            }
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
