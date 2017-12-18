<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171218100326 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * bin/console doctrine:cache:flush coreshop.doctrine.cache.pimcore
     * bin/console doctrine:schema:update --dump-sql --force
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        if ($schema->hasTable('coreshop_store')) {
            $table = $schema->getTable('coreshop_store');
            if (!$table->hasColumn('useGrossPrice')) {
                $table->addColumn('useGrossPrice', 'boolean', ['options' => ['default' => false], 'groups' => ['List', 'Detailed']]);
            }
        }

        $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
