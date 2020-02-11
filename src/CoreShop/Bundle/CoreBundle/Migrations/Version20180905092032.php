<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180905092032 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $adjustmentFields = [
            'pimcoreAdjustmentTotalNet' => [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'bigint(20)',
                'columnType' => 'bigint(20)',
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'pimcoreAdjustmentTotalNet',
                'title' => 'Adjustments Total Net',
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
            ],
            'pimcoreAdjustmentTotalGross' => [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'bigint(20)',
                'columnType' => 'bigint(20)',
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'pimcoreAdjustmentTotalGross',
                'title' => 'Adjustments Total Gross',
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
            ],
            'adjustmentItems' => [
                'fieldtype' => 'fieldcollections',
                'phpdocType' => '\\Pimcore\\Model\\DataObject\\Fieldcollection',
                'allowedTypes' => [
                    'CoreShopAdjustment',
                ],
                'lazyLoading' => true,
                'maxItems' => '',
                'disallowAddRemove' => false,
                'disallowReorder' => false,
                'collapsed' => false,
                'collapsible' => false,
                'name' => 'adjustmentItems',
                'title' => 'Adjustments',
                'tooltip' => '',
                'mandatory' => false,
                'noteditable' => true,
                'index' => false,
                'locked' => false,
                'style' => '',
                'permissions' => null,
                'datatype' => 'data',
                'columnType' => null,
                'queryColumnType' => null,
                'relationType' => false,
                'invisible' => false,
                'visibleGridView' => false,
                'visibleSearch' => false,
            ],
        ];

        $baseAdjustmentFields = [
            'basePimcoreAdjustmentTotalNet' => [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'bigint(20)',
                'columnType' => 'bigint(20)',
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'basePimcoreAdjustmentTotalNet',
                'title' => 'Adjustments Total Net',
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
            ],
            'basePimcoreAdjustmentTotalGross' => [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'bigint(20)',
                'columnType' => 'bigint(20)',
                'phpdocType' => 'int',
                'minValue' => null,
                'maxValue' => null,
                'name' => 'basePimcoreAdjustmentTotalGross',
                'title' => 'Adjustments Total Gross',
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
            ],
            'baseAdjustmentItems' => [
                'fieldtype' => 'fieldcollections',
                'phpdocType' => '\\Pimcore\\Model\\DataObject\\Fieldcollection',
                'allowedTypes' => [
                    'CoreShopAdjustment',
                ],
                'lazyLoading' => true,
                'maxItems' => '',
                'disallowAddRemove' => false,
                'disallowReorder' => false,
                'collapsed' => false,
                'collapsible' => false,
                'name' => 'baseAdjustmentItems',
                'title' => 'Adjustments',
                'tooltip' => '',
                'mandatory' => false,
                'noteditable' => true,
                'index' => false,
                'locked' => false,
                'style' => '',
                'permissions' => null,
                'datatype' => 'data',
                'columnType' => null,
                'queryColumnType' => null,
                'relationType' => false,
                'invisible' => false,
                'visibleGridView' => false,
                'visibleSearch' => false,
            ],
        ];

        $classesToUpdate = [
            'coreshop.model.order_invoice.pimcore_class_name',
        ];

        foreach ($classesToUpdate as $class) {
            $realClassName = $this->container->getParameter($class);
            $classUpdater = new ClassUpdate($realClassName);

            foreach ($adjustmentFields as $key => $field) {
                if (!$classUpdater->hasField($key)) {
                    $classUpdater->insertFieldAfter('totalGross', $field);
                }
            }

            foreach ($baseAdjustmentFields as $key => $field) {
                if (!$classUpdater->hasField($key)) {
                    $classUpdater->insertFieldAfter('baseTotalGross', $field);
                }
            }

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
