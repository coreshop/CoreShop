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

class Version20200415153636 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if (!$schema->getTable('coreshop_rule_condition')->hasColumn('sort')) {
            $this->addSql('ALTER TABLE coreshop_rule_condition ADD sort INT DEFAULT NULL;');
        }

        if (!$schema->getTable('coreshop_rule_action')->hasColumn('sort')) {
            $this->addSql('ALTER TABLE coreshop_rule_action ADD sort INT DEFAULT NULL;');
        }

        if (!$schema->getTable('coreshop_filter_condition')->hasColumn('sort')) {
            $this->addSql('ALTER TABLE coreshop_filter_condition ADD sort INT DEFAULT NULL;');
        }
    }

    public function down(Schema $schema): void
    {
    }
}
