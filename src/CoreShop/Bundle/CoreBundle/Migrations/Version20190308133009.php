<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190308133009 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO coreshop_product_store_values SELECT id, storeId as store, productId as product, price as price FROM coreshop_product_store_price WHERE property=\'storePrice\'');

        // remove storePrice tag from product class
        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $classUpdater = new ClassUpdate($productClass);

        if ($classUpdater->hasField('storePrice')) {
            $this->writeMessage(sprintf('You need to drop the StorePrice field manually from the class %s', $productClass));
        }

        $this->writeMessage('You need to drop the coreshop_product_store_price table manually, if you\'re not using the storePrice tag in other object definitions.');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
