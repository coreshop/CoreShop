<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180523085857 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $categoryClass = $this->container->getParameter('coreshop.model.category.pimcore_class_name');
        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');

        $categoryClassUpdater = new ClassUpdate($categoryClass);
        $productClassUpdater = new ClassUpdate($productClass);

        if (!$categoryClassUpdater->getProperty('linkGeneratorReference')) {
            $categoryClassUpdater->setProperty('linkGeneratorReference', '@coreshop.object.link_generator.category');
            $categoryClassUpdater->save();
        }

        if (!$productClassUpdater->getProperty('linkGeneratorReference')) {
            $productClassUpdater->setProperty('linkGeneratorReference', '@coreshop.object.link_generator.product');
            $productClassUpdater->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
