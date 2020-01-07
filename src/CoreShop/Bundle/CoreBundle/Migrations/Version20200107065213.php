<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException;
use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200107065213 extends AbstractPimcoreMigration
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     * @throws ClassDefinitionFieldNotFoundException
     * @throws ClassDefinitionNotFoundException
     */
    public function up(Schema $schema)
    {
        $maximumQuantityToOrderField = [
            'fieldtype' => 'numeric',
            'width' => '',
            'defaultValue' => NULL,
            'queryColumnType' => 'double',
            'columnType' => 'double',
            'phpdocType' => 'float',
            'integer' => true,
            'unsigned' => false,
            'minValue' => 1,
            'maxValue' => NULL,
            'unique' => false,
            'decimalSize' => 1,
            'decimalPrecision' => 1,
            'name' => 'maximumQuantityToOrder',
            'title' => 'Maximum Quantity To Order',
            'tooltip' => 'The maximum quantity that the user is permitted to order for this product',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => NULL,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $classUpdater = new ClassUpdate($productClass);
        if (!$classUpdater->hasField('maximumQuantityToOrder')) {
            $classUpdater->insertFieldAfter('minimumQuantityToOrder', $maximumQuantityToOrderField);
            $classUpdater->save();
        }

    }

    /**
     * @param Schema $schema
     * @throws ClassDefinitionNotFoundException
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function down(Schema $schema)
    {
        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $classUpdater = new ClassUpdate($productClass);
        if($classUpdater->hasField('maximumQuantityToOrder')) {
            $classUpdater->removeField('maximumQuantityToOrder');
            $classUpdater->save();
        }
    }
}
