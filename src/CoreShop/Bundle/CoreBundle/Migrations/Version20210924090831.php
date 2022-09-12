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

final class Version20210924090831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE coreshop_product_store_values CHANGE price price BIGINT NOT NULL COMMENT '(DC2Type:bigintInteger)';
            ALTER TABLE coreshop_product_unit_definition_price CHANGE price price BIGINT NOT NULL COMMENT '(DC2Type:bigintInteger)';
            ALTER TABLE coreshop_product_quantity_price_rule_range CHANGE amount amount BIGINT NOT NULL COMMENT '(DC2Type:bigintInteger)', CHANGE pseudo_price pseudo_price BIGINT NOT NULL COMMENT '(DC2Type:bigintInteger)';
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
