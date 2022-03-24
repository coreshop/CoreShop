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

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190320143949 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE coreshop_product_specific_price_rule_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, creationDate DATE NOT NULL, modificationDate DATETIME DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_30C732842C2AC5D3 (translatable_id), UNIQUE INDEX coreshop_product_specific_price_rule_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
            CREATE TABLE coreshop_product_price_rule_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, creationDate DATE NOT NULL, modificationDate DATETIME DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_C8C39DC12C2AC5D3 (translatable_id), UNIQUE INDEX coreshop_product_price_rule_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_general_ci ENGINE = InnoDB;
            ALTER TABLE coreshop_product_specific_price_rule_translation ADD CONSTRAINT FK_30C732842C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES coreshop_product_specific_price_rule (id) ON DELETE CASCADE;
            ALTER TABLE coreshop_product_price_rule_translation ADD CONSTRAINT FK_C8C39DC12C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES coreshop_product_price_rule (id) ON DELETE CASCADE;
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
