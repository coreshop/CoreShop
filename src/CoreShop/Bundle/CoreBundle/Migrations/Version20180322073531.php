<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180322073531 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        //update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');

        //update static routes (change password route added)
        $options = ['allowed' => ['coreshop_customer_change_password']];
        $this->container->get('coreshop.resource.installer.routes')->installResources(new NullOutput(), 'coreshop', $options);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
