<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180125192846 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $needsRecalculation = [
            'fieldtype' => 'checkbox',
            'defaultValue' => 0,
            'queryColumnType' => 'tinyint(1)',
            'columnType' => 'tinyint(1)',
            'phpdocType' => 'boolean',
            'name' => 'needsRecalculation',
            'title' => 'Needs Recalculation',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => true,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $cart = $this->container->getParameter('coreshop.model.cart.pimcore_class_name');
        $classUpdater = new ClassUpdate($cart);
        if (!$classUpdater->hasField('needsRecalculation')) {
            $classUpdater->insertFieldAfter('items', $needsRecalculation);
            $classUpdater->save();
        }

        //update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}