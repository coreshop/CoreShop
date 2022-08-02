<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200421105731 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $orderItemClassName = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderItemClassName);

        $fieldCustomItemDiscount = [
            'fieldtype' => 'numeric',
            'width' => '',
            'defaultValue' => null,
            'queryColumnType' => 'double',
            'columnType' => 'double',
            'phpdocType' => 'float',
            'integer' => false,
            'unsigned' => false,
            'minValue' => null,
            'maxValue' => null,
            'unique' => false,
            'decimalSize' => null,
            'decimalPrecision' => null,
            'name' => 'customItemDiscount',
            'title' => 'coreshop.order_item.custom_item_discount',
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
            'visibleSearch' => false,
            'defaultValueGenerator' => '',
        ];

        $fieldCustomItemPrice = [
            'fieldtype' => 'coreShopMoney',
            'width' => '',
            'defaultValue' => null,
            'phpdocType' => 'int',
            'minValue' => null,
            'maxValue' => null,
            'name' => 'customItemPrice',
            'title' => 'coreshop.order_item.custom_item_price',
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
            'visibleSearch' => false,
        ];

        $fieldConvertedCustomItemPrice = [
            'fieldtype' => 'coreShopMoney',
            'width' => '',
            'defaultValue' => null,
            'phpdocType' => 'int',
            'minValue' => null,
            'maxValue' => null,
            'name' => 'convertedCustomItemPrice',
            'title' => 'coreshop.order_item.converted_custom_item_price',
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
            'visibleSearch' => false,
        ];

        if (!$classUpdater->hasField('customItemDiscount')) {
            $classUpdater->insertFieldAfter('unit', $fieldCustomItemDiscount);
        }

        if (!$classUpdater->hasField('customItemPrice')) {
            $classUpdater->insertFieldAfter('itemWholesalePrice', $fieldCustomItemPrice);
        }

        if (!$classUpdater->hasField('convertedCustomItemPrice')) {
            $classUpdater->insertFieldAfter('convertedItemWholesalePrice', $fieldConvertedCustomItemPrice);
        }

        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
