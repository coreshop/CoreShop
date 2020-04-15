<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415152607 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->writeMessage('Start migration for Order Item Class Fields');

        $orderItemClassName = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');

        $classUpdater = new ClassUpdate($orderItemClassName);

        $fields = [
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedItemWholesalePrice',
                'title' => 'coreshop.order_item.converted_item_wholesale_price',
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
                'name' => 'convertedItemDiscountNet',
                'title' => 'coreshop.order_item.converted_item_discount_net',
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
                'name' => 'convertedItemDiscountGross',
                'title' => 'coreshop.order_item.converted_item_discount_gross',
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
                'name' => 'convertedItemDiscountPriceNet',
                'title' => 'coreshop.order_item.converted_item_discount_price_net',
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
                'name' => 'convertedItemDiscountPriceGross',
                'title' => 'coreshop.order_item.converted_item_discount_price_gross',
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
                'name' => 'convertedItemPriceNet',
                'title' => 'coreshop.order_item.converted_item_price_net',
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
                'name' => 'convertedItemPriceGross',
                'title' => 'coreshop.order_item.converted_item_price_gross',
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
                'name' => 'convertedItemRetailPriceNet',
                'title' => 'coreshop.order_item.converted_item_retail_price_net',
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
                'name' => 'convertedItemRetailPriceGross',
                'title' => 'coreshop.order_item.converted_item_retail_price_gross',
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
                'title' => 'coreshop.order_item.converted_total_net',
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
                'title' => 'coreshop.order_item.converted_total_gross',
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
                'name' => 'convertedItemTax',
                'title' => 'coreshop.order_item.converted_item_tax',
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
                'title' => 'coreshop.order_item.converted_taxes',
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
                'title' => 'coreshop.order_item.converted_adjustments',
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
                'title' => 'coreshop.order_item.converted_adjustments_total_gross',
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
                'title' => 'coreshop.order_item.converted_adjustments_total_net',
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
        $fieldBefore = 'adjustmentItems';

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
