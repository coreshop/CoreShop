<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171209110814 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     * @throws \CoreShop\Component\Pimcore\ClassDefinitionFieldNotFoundException
     */
    public function up(Schema $schema)
    {
        $additionalDataField = [
            'fieldtype'       => 'objectbricks',
            'phpdocType'      => '\\Pimcore\\Model\\DataObject\\Objectbrick',
            'allowedTypes'    => [],
            'maxItems'        => '',
            'name'            => 'additionalData',
            'title'           => 'Additional Data',
            'tooltip'         => '',
            'mandatory'       => false,
            'noteditable'     => true,
            'index'           => false,
            'locked'          => null,
            'style'           => '',
            'permissions'     => null,
            'datatype'        => 'data',
            'columnType'      => null,
            'queryColumnType' => null,
            'relationType'    => false,
            'invisible'       => false,
            'visibleGridView' => false,
            'visibleSearch'   => false,
        ];

        $cartClass = $this->container->getParameter('coreshop.model.cart.pimcore_class_name');
        $classUpdater = new ClassUpdate($cartClass);
        if (!$classUpdater->hasField('additionalData')) {
            $classUpdater->insertFieldAfter('comment', $additionalDataField);
            $classUpdater->save();
        }

        $orderClass = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderClass);
        if (!$classUpdater->hasField('additionalData')) {
            $classUpdater->insertFieldAfter('comment', $additionalDataField);
            $classUpdater->save();
        }

        $quoteClass = $this->container->getParameter('coreshop.model.quote.pimcore_class_name');
        $classUpdater = new ClassUpdate($quoteClass);
        if (!$classUpdater->hasField('additionalData')) {
            $classUpdater->insertFieldAfter('comment', $additionalDataField);
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