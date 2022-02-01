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

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171209154519 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $fields = [
            'onHold' => [
                'fieldtype' => 'numeric',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'double',
                'columnType' => 'double',
                'phpdocType' => 'float',
                'integer' => true,
                'unsigned' => false,
                'minValue' => null,
                'maxValue' => null,
                'unique' => false,
                'decimalSize' => null,
                'decimalPrecision' => null,
                'name' => 'onHold',
                'title' => 'On Hold',
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
            ],
            'onHand' => [
                'fieldtype' => 'numeric',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'double',
                'columnType' => 'double',
                'phpdocType' => 'float',
                'integer' => true,
                'unsigned' => false,
                'minValue' => null,
                'maxValue' => null,
                'unique' => false,
                'decimalSize' => null,
                'decimalPrecision' => null,
                'name' => 'onHand',
                'title' => 'On Hand',
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
            ],
            'isTracked' => [
                'fieldtype' => 'checkbox',
                'defaultValue' => 0,
                'queryColumnType' => 'tinyint(1)',
                'columnType' => 'tinyint(1)',
                'phpdocType' => 'boolean',
                'name' => 'isTracked',
                'title' => 'Is Tracked',
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
            ],
        ];

        $cartClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');

        $classUpdater = new ClassUpdate($cartClass);

        if ($classUpdater->hasField('quantity')) {
            $classUpdater->replaceFieldProperties('quantity', ['noteditable' => true, 'invisible' => true]);
        }

        if ($classUpdater->hasField('isAvailableWhenOutOfStock')) {
            $classUpdater->replaceFieldProperties('isAvailableWhenOutOfStock', ['noteditable' => true, 'invisible' => true]);
        }

        if (!$classUpdater->hasField('comment')) {
            foreach ($fields as $fieldName => $field) {
                if ($classUpdater->hasField($fieldName)) {
                    continue;
                }

                $classUpdater->insertFieldAfter('isAvailableWhenOutOfStock', $field);
            }
        }

        $classUpdater->save();

        $products = $this->container->get('coreshop.repository.product')->getList();

        foreach ($products as $product) {
            if (!$product instanceof ProductInterface) {
                return;
            }

            $product->setOnHand($product->getQuantity());

            if (method_exists($product, 'getIsAvailableWhenOutOfStock')) {
                $product->setIsTracked($product->getIsAvailableWhenOutOfStock());
            }

            $product->save();
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
