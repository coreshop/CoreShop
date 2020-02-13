<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200213132916 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('alter table coreshop_carrier add cartBasedTaxation tinyint(1) not null after logo;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // do nothing due to potential data loss
    }
}
