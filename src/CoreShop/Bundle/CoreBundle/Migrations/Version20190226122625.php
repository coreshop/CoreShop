<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190226122625 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // install units and unit definitions
        if (!$schema->hasTable('coreshop_product_unit')) {
            $this->addSql('
                CREATE TABLE coreshop_product_unit_definitions (id INT AUTO_INCREMENT NOT NULL, default_unit_definition INT DEFAULT NULL, product INT NOT NULL COMMENT \'(DC2Type:pimcoreObject)\', UNIQUE INDEX UNIQ_5D50CA20D34A04AD (product), UNIQUE INDEX UNIQ_5D50CA20F3CE11C4 (default_unit_definition), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
                CREATE TABLE coreshop_product_unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(190) NOT NULL, creationDate DATE NOT NULL, modificationDate DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_803A19D05E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
                CREATE TABLE coreshop_product_unit_definition (id INT AUTO_INCREMENT NOT NULL, product_unit_definitions INT DEFAULT NULL, unit INT DEFAULT NULL, conversion_rate DOUBLE PRECISION DEFAULT NULL, INDEX IDX_37BB52AF8685AB18 (product_unit_definitions), INDEX IDX_37BB52AFDCBB0C53 (unit), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
                CREATE TABLE coreshop_product_unit_definition_price (id INT AUTO_INCREMENT NOT NULL, unit_definition INT DEFAULT NULL, product_store_values INT DEFAULT NULL, price INT NOT NULL, INDEX IDX_13ECB5B6B98B918 (unit_definition), INDEX IDX_13ECB5BD314F81B (product_store_values), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
                ALTER TABLE coreshop_product_unit_definitions ADD CONSTRAINT FK_5D50CA20F3CE11C4 FOREIGN KEY (default_unit_definition) REFERENCES coreshop_product_unit_definition (id) ON DELETE SET NULL;
                ALTER TABLE coreshop_product_unit_definition ADD CONSTRAINT FK_37BB52AF8685AB18 FOREIGN KEY (product_unit_definitions) REFERENCES coreshop_product_unit_definitions (id) ON DELETE CASCADE;
                ALTER TABLE coreshop_product_unit_definition ADD CONSTRAINT FK_37BB52AFDCBB0C53 FOREIGN KEY (unit) REFERENCES coreshop_product_unit (id);
                ALTER TABLE coreshop_product_unit_definition_price ADD CONSTRAINT FK_13ECB5B6B98B918 FOREIGN KEY (unit_definition) REFERENCES coreshop_product_unit_definition (id) ON DELETE CASCADE;
                ALTER TABLE coreshop_product_unit_definition_price ADD CONSTRAINT FK_13ECB5BD314F81B FOREIGN KEY (product_store_values) REFERENCES coreshop_product_store_values (id) ON DELETE CASCADE;
                ALTER TABLE coreshop_product_quantity_price_rule_range ADD unit_definition INT DEFAULT NULL;
                ALTER TABLE coreshop_product_quantity_price_rule_range ADD CONSTRAINT FK_C6BA05DA6B98B918 FOREIGN KEY (unit_definition) REFERENCES coreshop_product_unit_definition (id);
                CREATE INDEX IDX_C6BA05DA6B98B918 ON coreshop_product_quantity_price_rule_range (unit_definition);
                CREATE UNIQUE INDEX definitions_and_unit ON coreshop_product_unit_definition (product_unit_definitions, unit);
            ');
        }

        $unitDefinitionsField = [
            'fieldtype' => 'coreShopProductUnitDefinitions',
            'width' => '',
            'defaultValue' => null,
            'phpdocType' => 'array',
            'name' => 'unitDefinitions',
            'title' => 'Product Unit Definitions',
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
        if (!$classUpdater->hasField('unitDefinitions')) {
            $classUpdater->insertFieldBefore('wholesalePrice', $unitDefinitionsField);
            $classUpdater->save();
        }

        $unitDefinitionField = [
             'fieldtype' => 'coreShopProductUnitDefinition',
             'phpdocType' => '\\CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionInterface',
             'allowEmpty' => false,
             'name' => 'unitDefinition',
             'title' => 'Unit Definition',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
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

        $cartItemClass = $this->container->getParameter('coreshop.model.cart_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($cartItemClass);
        if (!$classUpdater->hasField('unitDefinition')) {
            $classUpdater->insertFieldAfter('digitalProduct', $unitDefinitionField);
            $classUpdater->save();
        }

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
