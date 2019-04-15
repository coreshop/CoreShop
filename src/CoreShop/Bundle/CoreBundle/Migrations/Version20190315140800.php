<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190315140800 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException
     */
    public function up(Schema $schema)
    {
        $defaultUnitQuantityField = [
            'fieldtype' => 'numeric',
            'width' => '',
            'defaultValue' => null,
            'integer' => false,
            'unsigned' => false,
            'minValue' => null,
            'maxValue' => null,
            'decimalPrecision' => null,
            'name' => 'defaultUnitQuantity',
            'title' => 'Default Unit Quantity',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        foreach (['order_item', 'cart_item', 'quote_item'] as $itemClass) {
            $itemClass = $this->container->getParameter(sprintf('coreshop.model.%s.pimcore_class_name', $itemClass));
            $classUpdater = new ClassUpdate($itemClass);

            if (!$classUpdater->hasField('defaultUnitQuantity')) {
                $classUpdater->insertFieldAfter('quantity', $defaultUnitQuantityField);
                $classUpdater->save();
            }
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
