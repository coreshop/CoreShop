<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20210614115749 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        if (!$schema->getTable('coreshop_rule_condition')->hasColumn('sort')) {
            $this->addSql('ALTER TABLE coreshop_rule_condition ADD sort INT DEFAULT NULL;');
        }

        if (!$schema->getTable('coreshop_rule_action')->hasColumn('sort')) {
            $this->addSql('ALTER TABLE coreshop_rule_action ADD sort INT DEFAULT NULL;');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }
}
