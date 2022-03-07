<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190114155520 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('coreshop_product_quantity_price_rule')) {
            $this->addSql('CREATE TABLE coreshop_product_quantity_price_rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, priority INT NOT NULL, product INT NOT NULL, calculation_behaviour VARCHAR(255) NOT NULL, creationDate DATETIME NOT NULL, modificationDate DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('CREATE TABLE coreshop_product_quantity_price_rule_conditions (product_quantity_price_rule_id INT NOT NULL, condition_id INT NOT NULL, INDEX IDX_1AD1944FCCF4F3B6 (product_quantity_price_rule_id), INDEX IDX_1AD1944F887793B6 (condition_id), PRIMARY KEY(product_quantity_price_rule_id, condition_id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('CREATE TABLE coreshop_product_quantity_price_rule_range (id INT AUTO_INCREMENT NOT NULL, rule_id INT DEFAULT NULL, range_from INT NOT NULL, range_to INT NOT NULL, pricing_behaviour VARCHAR(255) NOT NULL, percentage DOUBLE PRECISION NOT NULL, highlighted TINYINT(1) NOT NULL, amount INT NOT NULL, pseudo_price INT NOT NULL, currencyId INT DEFAULT NULL, INDEX IDX_C6BA05DA744E0351 (rule_id), INDEX IDX_C6BA05DA91000B8A (currencyId), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;');
            $this->addSql('ALTER TABLE coreshop_product_quantity_price_rule_conditions ADD CONSTRAINT FK_1AD1944FCCF4F3B6 FOREIGN KEY (product_quantity_price_rule_id) REFERENCES coreshop_product_quantity_price_rule (id) ON DELETE CASCADE;');
            $this->addSql('ALTER TABLE coreshop_product_quantity_price_rule_conditions ADD CONSTRAINT FK_1AD1944F887793B6 FOREIGN KEY (condition_id) REFERENCES coreshop_rule_condition (id) ON DELETE CASCADE;');
            $this->addSql('ALTER TABLE coreshop_product_quantity_price_rule_range ADD CONSTRAINT FK_C6BA05DA744E0351 FOREIGN KEY (rule_id) REFERENCES coreshop_product_quantity_price_rule (id);');
            $this->addSql('ALTER TABLE coreshop_product_quantity_price_rule_range ADD CONSTRAINT FK_C6BA05DA91000B8A FOREIGN KEY (currencyId) REFERENCES coreshop_currency (id) ON DELETE SET NULL;');
        }

        $quantityPriceRulesField = [
            'fieldtype' => 'coreShopProductQuantityPriceRules',
            'height' => null,
            'name' => 'quantityPriceRules',
            'title' => 'Quantity Price Rules',
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
        if (!$classUpdater->hasField('quantityPriceRules')) {
            $classUpdater->insertFieldAfter('specificPriceRules', $quantityPriceRulesField);
            $classUpdater->save();
        }

        //update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');
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
