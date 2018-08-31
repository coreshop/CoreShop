<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180527091927 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE coreshop_product_store_price ADD `property` VARCHAR(255) NOT NULL;');
        $this->addSql(sprintf('UPDATE coreshop_product_store_price SET `property`=\'%s\';', 'storePrice'));

        $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
