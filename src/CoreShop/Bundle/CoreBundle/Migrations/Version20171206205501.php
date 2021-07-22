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

class Version20171206205501 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('coreshop_carrier_translation')) {
            $this->connection->exec('CREATE TABLE coreshop_carrier_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, description LONGTEXT DEFAULT NULL, creationDate DATETIME NOT NULL, modificationDate DATETIME DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_CE09FC1C2C2AC5D3 (translatable_id), UNIQUE INDEX coreshop_carrier_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;');
            $this->connection->exec('ALTER TABLE coreshop_carrier_translation ADD CONSTRAINT FK_CE09FC1C2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES coreshop_carrier (id) ON DELETE CASCADE;');
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
