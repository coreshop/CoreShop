<?php

namespace CoreShop\Bundle\AdminBundle\Migrations;

use CoreShop\Bundle\AdminBundle\CoreShopAdminBundle;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171124111923 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->container->get(PimcoreBundleManager::class)->disable(CoreShopAdminBundle::class);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
