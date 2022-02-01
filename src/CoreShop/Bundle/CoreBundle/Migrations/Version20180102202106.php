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

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use CoreShop\Component\Product\Model\ProductInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102202106 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException
     * @throws \Exception
     */
    public function up(Schema $schema)
    {
        $objectIdField = [
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
            'name' => 'objectId',
            'title' => 'Object Id',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $mainObjectIdField = [
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
            'name' => 'mainObjectId',
            'title' => 'Main Object Id',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $orderItem = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderItem);
        if (!$classUpdater->hasField('mainObjectId')) {
            $classUpdater->insertFieldAfter('product', $mainObjectIdField);
            $classUpdater->save();
        }

        if (!$classUpdater->hasField('objectId')) {
            $classUpdater->insertFieldAfter('product', $objectIdField);
            $classUpdater->save();
        }

        $quoteItem = $this->container->getParameter('coreshop.model.quote_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($quoteItem);
        if (!$classUpdater->hasField('mainObjectId')) {
            $classUpdater->insertFieldAfter('product', $mainObjectIdField);
            $classUpdater->save();
        }

        if (!$classUpdater->hasField('objectId')) {
            $classUpdater->insertFieldAfter('product', $objectIdField);
            $classUpdater->save();
        }

        \Pimcore::collectGarbage();

        //update existing orders
        $orderItems = $this->container->get('coreshop.repository.order_item')->getList();

        /** @var OrderItemInterface $object */
        foreach ($orderItems as $object) {
            $productObject = $object->getProduct();
            if (!$productObject instanceof ProductInterface) {
                continue;
            }

            $object->setObjectId($productObject->getId());
            $mainObjectId = null;

            if ($productObject->getType() === 'variant') {
                $mainProduct = $productObject;
                while ($mainProduct->getType() === 'variant') {
                    $mainProduct = $mainProduct->getParent();
                }

                $object->setMainObjectId($mainProduct->getId());
            }

            $object->save();
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
