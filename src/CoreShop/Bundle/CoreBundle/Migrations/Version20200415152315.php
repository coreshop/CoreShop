<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415152315 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->writeMessage('Start migration for Order Class Fields');

        $orderClassName = $this->container->getParameter('coreshop.model.order.pimcore_class_name');

        $classUpdater = new ClassUpdate($orderClassName);

        $fields = [
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedPaymentTotal',
                'title' => 'coreshop.order.converted_payment_total',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedTotalNet',
                'title' => 'coreshop.order.converted_total_net',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedTotalGross',
                'title' => 'coreshop.order.converted_total_gross',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedSubtotalNet',
                'title' => 'coreshop.order.converted_subtotal_net',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedSubtotalGross',
                'title' => 'coreshop.order.converted_subtotal_gross',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'fieldcollections',
                'phpdocType' => '\\Pimcore\\Model\\DataObject\\Fieldcollection',
                'allowedTypes' => [
                    'CoreShopTaxItem'
                ],
                'lazyLoading' => true,
                'maxItems' => '',
                'disallowAddRemove' => false,
                'disallowReorder' => false,
                'collapsed' => false,
                'collapsible' => false,
                'border' => false,
                'name' => 'convertedTaxes',
                'title' => 'coreshop.order.converted_taxes',
                'tooltip' => '',
                'mandatory' => false,
                'noteditable' => false,
                'index' => false,
                'locked' => false,
                'style' => '',
                'permissions' => null,
                'datatype' => 'data',
                'relationType' => false,
                'invisible' => false,
                'visibleGridView' => false,
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedPimcoreAdjustmentTotalNet',
                'title' => 'coreshop.order.converted_adjustments_total_net',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedPimcoreAdjustmentTotalGross',
                'title' => 'coreshop.order.converted_adjustments_total_gross',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'fieldcollections',
                'phpdocType' => '\\Pimcore\\Model\\DataObject\\Fieldcollection',
                'allowedTypes' => [
                    'CoreShopAdjustment'
                ],
                'lazyLoading' => true,
                'maxItems' => '',
                'disallowAddRemove' => false,
                'disallowReorder' => false,
                'collapsed' => false,
                'collapsible' => false,
                'border' => false,
                'name' => 'convertedAdjustmentItems',
                'title' => 'coreshop.order.converted_adjustments',
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
                'visibleSearch' => false
            ]
        ];

        $save = false;
        $fieldBefore = 'subtotalGross';

        foreach ($fields as $field) {
            if ($classUpdater->hasField($field['name'])) {
                $fieldBefore = $field['name'];

                $this->writeMessage(
                    sprintf('Field "%s" already found, skipping', $field['name'])
                );

                continue;
            }

            $classUpdater->insertFieldAfter($fieldBefore, $field);

            $save = true;
            $fieldBefore = $field['name'];
        }

        if ($save) {
            $classUpdater->save();
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
