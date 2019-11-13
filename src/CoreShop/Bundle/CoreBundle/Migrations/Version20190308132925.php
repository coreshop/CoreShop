<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190308132925 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // install store values
        if (!$schema->hasTable('coreshop_product_store_values')) {
            $this->addSql('CREATE TABLE coreshop_product_store_values (id INT AUTO_INCREMENT NOT NULL, store INT DEFAULT NULL, product INT NOT NULL COMMENT \'(DC2Type:pimcoreObject)\', price INT NOT NULL, INDEX IDX_9EED0E97FF575877 (store), UNIQUE INDEX product_store (product, store), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('ALTER TABLE coreshop_product_store_values ADD CONSTRAINT FK_9EED0E97FF575877 FOREIGN KEY (store) REFERENCES coreshop_store (id) ON DELETE SET NULL;');
            $this->addSql('ALTER TABLE coreshop_product_unit_definition_price ADD CONSTRAINT FK_13ECB5BD314F81B FOREIGN KEY (product_store_values) REFERENCES coreshop_product_store_values (id) ON DELETE CASCADE;');
        }

        $storeValuesField = [
             'fieldtype' => 'coreShopStoreValues',
             'width' => '',
             'defaultValue' => null,
             'phpdocType' => 'array',
             'minValue' => null,
             'maxValue' => null,
             'name' => 'storeValues',
             'title' => 'Store Values',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => false,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => null,
             'datatype' => 'data',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
        ];

        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $classUpdater = new ClassUpdate($productClass);
        if (!$classUpdater->hasField('storeValues')) {
            $classUpdater->insertFieldAfter('unitDefinitions', $storeValuesField);
            $classUpdater->save();
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
