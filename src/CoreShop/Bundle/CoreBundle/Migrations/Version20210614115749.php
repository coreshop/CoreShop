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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20210614115749 extends AbstractMigration
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
