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

class Version20180212184829 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $itemDiscountPriceNet = [
            'fieldtype' => 'coreShopMoney',
            'width' => '',
            'defaultValue' => null,
            'queryColumnType' => 'bigint(20)',
            'columnType' => 'bigint(20)',
            'phpdocType' => 'int',
            'minValue' => null,
            'maxValue' => null,
            'name' => 'itemDiscountPriceNet',
            'title' => 'Item Discount Price Net',
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

        $itemDiscountPriceGross = [
            'fieldtype' => 'coreShopMoney',
            'width' => '',
            'defaultValue' => null,
            'queryColumnType' => 'bigint(20)',
            'columnType' => 'bigint(20)',
            'phpdocType' => 'int',
            'minValue' => null,
            'maxValue' => null,
            'name' => 'itemDiscountPriceGross',
            'title' => 'Item Discount Price Gross',
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

        $itemDiscountNet = [
            'fieldtype' => 'coreShopMoney',
            'width' => '',
            'defaultValue' => null,
            'queryColumnType' => 'bigint(20)',
            'columnType' => 'bigint(20)',
            'phpdocType' => 'int',
            'minValue' => null,
            'maxValue' => null,
            'name' => 'itemDiscountNet',
            'title' => 'Item Discount Price Net',
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

        $itemDiscountGross = [
            'fieldtype' => 'coreShopMoney',
            'width' => '',
            'defaultValue' => null,
            'queryColumnType' => 'bigint(20)',
            'columnType' => 'bigint(20)',
            'phpdocType' => 'int',
            'minValue' => null,
            'maxValue' => null,
            'name' => 'itemDiscountGross',
            'title' => 'Item Discount Price Gross',
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

        $cartClass = $this->container->getParameter('coreshop.model.cart_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($cartClass);
        if (!$classUpdater->hasField('itemDiscountPriceNet')) {
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountPriceNet);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountPriceGross);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountNet);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountGross);
            $classUpdater->save();
        }

        $orderClass = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderClass);
        if (!$classUpdater->hasField('itemDiscountPriceNet')) {
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountPriceNet);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountPriceGross);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountNet);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountGross);
            $classUpdater->save();
        }

        $quoteClass = $this->container->getParameter('coreshop.model.quote_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($quoteClass);
        if (!$classUpdater->hasField('itemDiscountPriceNet')) {
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountPriceNet);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountPriceGross);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountNet);
            $classUpdater->insertFieldAfter('itemRetailPriceGross', $itemDiscountGross);
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
