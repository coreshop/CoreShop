<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200617120130 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        if (!$schema->getTable('coreshop_cart_price_rule_voucher_code')->hasColumn('creditUsed')) {
            $this->addSql('
                ALTER TABLE coreshop_cart_price_rule_voucher_code ADD creditUsed INT NOT NULL, ADD isCreditCode TINYINT(1) NOT NULL;
            ');
        }

        if (!$schema->getTable('coreshop_cart_price_rule_voucher_code')->hasColumn('creditAvailable')) {
            $this->addSql('
                ALTER TABLE coreshop_cart_price_rule_voucher_code ADD creditAvailable INT NOT NULL, ADD currencyId INT DEFAULT NULL;
            ');
        }

        if (!$schema->getTable('coreshop_cart_price_rule_voucher_code')->hasColumn('currencyId')) {
            $this->addSql('
                ALTER TABLE coreshop_cart_price_rule_voucher_code ADD CONSTRAINT FK_4AF500A991000B8A FOREIGN KEY (currencyId) REFERENCES coreshop_currency (id) ON DELETE SET NULL;
                CREATE INDEX IDX_4AF500A991000B8A ON coreshop_cart_price_rule_voucher_code (currencyId);
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
