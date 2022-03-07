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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190320175709 extends AbstractPimcoreMigration implements ContainerAwareInterface
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
        $itemQuantityFactor = [
            'fieldtype' => 'numeric',
            'width' => '',
            'defaultValue' => 0,
            'queryColumnType' => 'double',
            'columnType' => 'double',
            'phpdocType' => 'float',
            'integer' => true,
            'unsigned' => false,
            'minValue' => 1,
            'maxValue' => null,
            'unique' => false,
            'decimalSize' => 1,
            'decimalPrecision' => 1,
            'name' => 'itemQuantityFactor',
            'title' => 'Item Quantity Factor',
            'tooltip' => 'If you\'re calculating with a different item quantity, you\'re able to define a different item quantity factor. For example, if you want to display a "Price per 1000 Items" label, you need to change the item quantity factor to "1000". Be aware that CoreShop will recalculate the item price by using the "(default item price) / (item quantity factor)" formula.',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => 'padding: 10px 0;',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $minimumQuantityToOrderField = [
            'fieldtype' => 'fieldset',
            'labelWidth' => 150,
            'name' => 'Quantity Restrictions',
            'type' => null,
            'region' => null,
            'title' => 'Quantity Restrictions',
            'width' => null,
            'height' => null,
            'collapsible' => false,
            'collapsed' => false,
            'bodyStyle' => '',
            'datatype' => 'layout',
            'permissions' => null,
            'childs' => [
                    [
                        'fieldtype' => 'numeric',
                        'width' => '',
                        'defaultValue' => 0,
                        'queryColumnType' => 'double',
                        'columnType' => 'double',
                        'phpdocType' => 'float',
                        'integer' => true,
                        'unsigned' => false,
                        'minValue' => 0,
                        'maxValue' => null,
                        'unique' => false,
                        'decimalSize' => 1,
                        'decimalPrecision' => 1,
                        'name' => 'minimumQuantityToOrder',
                        'title' => 'Minimum Quantity To Order',
                        'tooltip' => 'The minimum quantity that the user must order for this product',
                        'mandatory' => false,
                        'noteditable' => false,
                        'index' => false,
                        'locked' => null,
                        'style' => '',
                        'permissions' => null,
                        'datatype' => 'data',
                        'relationType' => false,
                        'invisible' => false,
                        'visibleGridView' => false,
                        'visibleSearch' => false,
                    ],
                ],
            'locked' => false,
        ];

        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $classUpdater = new ClassUpdate($productClass);

        if (!$classUpdater->hasField('itemQuantityFactor')) {
            $classUpdater->insertFieldAfter('storeValues', $itemQuantityFactor);
        }

        if (!$classUpdater->hasField('minimumQuantityToOrder')) {
            $classUpdater->insertFieldAfter('isTracked', $minimumQuantityToOrderField);
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
