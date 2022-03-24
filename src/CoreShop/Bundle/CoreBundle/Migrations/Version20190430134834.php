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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190430134834 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($schema->hasTable('coreshop_product_store_price')) {
            $this->addSql('ALTER TABLE coreshop_product_store_price CHANGE property property VARCHAR(190) NOT NULL;');
            $this->addSql('CREATE INDEX IDX_514E3EBF367996058BF21CDE ON coreshop_product_store_price (productId, property);');
            $this->addSql('CREATE INDEX IDX_514E3EBF367996052F738A528BF21CDE ON coreshop_product_store_price (productId, storeId, property);');
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
