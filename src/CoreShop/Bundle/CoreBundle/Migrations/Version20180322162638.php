<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180322162638 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $cartClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');

        $classUpdater = new ClassUpdate($cartClass);

        if ($classUpdater->hasField('onHold')) {
            $classUpdater->replaceFieldProperties('onHold', ['noteditable' => true]);
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
