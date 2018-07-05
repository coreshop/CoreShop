<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171214105719 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        //update voucher index
        if ($schema->hasTable('coreshop_cart_price_rule')) {
            $table = $schema->getTable('coreshop_cart_price_rule');
            if (!$table->hasColumn('isVoucherRule') && $table->hasColumn('highlight')) {
                $table->dropColumn('highlight');
                $table->addColumn('isVoucherRule', 'boolean', ['expose' => true, 'groups' => ['Detailed']]);
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
