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

class Version20190902090625 extends AbstractPimcoreMigration
{
    public function up(Schema $schema)
    {
        if ($schema->hasTable('coreshop_product_unit_definition')) {
            $table = $schema->getTable('coreshop_product_unit_definition');
            if (!$table->hasColumn('precision')) {
                $this->addSql('ALTER TABLE coreshop_product_unit_definition ADD `precision` INT NOT NULL;');
            }
        }

        if ($schema->hasTable('coreshop_product_quantity_price_rule_range')) {
            $this->addSql('ALTER TABLE coreshop_product_quantity_price_rule_range CHANGE range_starting_from range_starting_from DOUBLE PRECISION NOT NULL;');
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
