<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200617120130 extends AbstractMigration
{
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

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
