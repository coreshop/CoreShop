<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

class Version20190913065635 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('users_permission_definitions')) {
            return;
        }

        $table = $schema->getTable('users_permission_definitions');

        if ($table->hasColumn('category')) {
            $this->addSql('UPDATE users_permission_definitions SET category=\'coreshop_permission_group_coreshop\' WHERE `key` LIKE \'coreshop_%\'');
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
