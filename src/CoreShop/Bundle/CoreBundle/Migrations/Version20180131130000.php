<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180131130000 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $customerGroup = $this->container->getParameter('coreshop.model.customer_group.pimcore_class_name');
        $classUpdater = new ClassUpdate($customerGroup);

        if ($classUpdater->getProperty('parentClass') === 'CoreShop\\Component\\Core\\Model\\CustomerGroup') {
            $classUpdater->setProperty('parentClass', 'CoreShop\\Component\\Customer\\Model\\CustomerGroup');
            $classUpdater->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
