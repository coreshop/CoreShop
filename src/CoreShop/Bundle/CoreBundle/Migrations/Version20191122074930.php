<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20191122074930 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
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
    public function down(Schema $schema)
    {
        // do nothing due to potential data loss
    }
}
