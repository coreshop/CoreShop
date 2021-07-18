<?php

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200224161101 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql(' ALTER TABLE coreshop_index ADD indexLastVersion TINYINT(1) DEFAULT \'0\' NOT NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
