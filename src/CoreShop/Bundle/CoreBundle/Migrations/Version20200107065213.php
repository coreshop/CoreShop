<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException;
use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200107065213 extends AbstractPimcoreMigration implements ContainerAwareInterface
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
     */
    public function down(Schema $schema)
    {
        // do nothing due to potential data loss
    }
}
