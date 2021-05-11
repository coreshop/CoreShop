<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20191122074930 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        if (!$schema->getTable('coreshop_carrier')->hasColumn('taxCalculationStrategy')) {
            $this->addSql('ALTER TABLE coreshop_carrier ADD `taxCalculationStrategy` VARCHAR(255) DEFAULT NULL AFTER logo;');
            $this->addSql("UPDATE coreshop_carrier SET `taxCalculationStrategy` = 'taxRule' WHERE `taxRuleGroupId` IS NOT NULL;");

            $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // do nothing due to potential data loss
    }
}
