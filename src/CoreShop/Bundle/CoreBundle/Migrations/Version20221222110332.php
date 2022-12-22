<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221222110332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if ($schema->getTable('coreshop_payment')->hasColumn('client_email') === false) {
            $this->addSql('ALTER TABLE coreshop_payment ADD client_email VARCHAR(255) DEFAULT NULL');
        }

        if ($schema->getTable('coreshop_payment')->hasColumn('client_id') === false) {
            $this->addSql('ALTER TABLE coreshop_payment ADD client_id VARCHAR(255) DEFAULT NULL;');
        }

        $this->addSql('ALTER TABLE coreshop_payment CHANGE details details JSON NOT NULL;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
