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
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190325142544 extends AbstractPimcoreMigration implements ContainerAwareInterface
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
        $unitField = [
             'fieldtype' => 'coreShopProductUnit',
             'allowEmpty' => true,
             'options' => null,
             'width' => null,
             'defaultValue' => null,
             'optionsProviderClass' => null,
             'optionsProviderData' => null,
             'queryColumnType' => 'varchar',
             'columnType' => 'varchar',
             'columnLength' => 190,
             'phpdocType' => 'string',
             'name' => 'unit',
             'title' => 'Unit',
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

        $unitIdentifierField = [
             'fieldtype' => 'input',
             'width' => null,
             'queryColumnType' => 'varchar',
             'columnType' => 'varchar',
             'columnLength' => 190,
             'phpdocType' => 'string',
             'regex' => '',
             'unique' => false,
             'showCharCount' => false,
             'name' => 'unitIdentifier',
             'title' => 'Unit Identifier',
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

        $quoteItemClass = $this->container->getParameter('coreshop.model.quote_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($quoteItemClass);
        if (!$classUpdater->hasField('unitIdentifier')) {
            $classUpdater->insertFieldAfter('digitalProduct', $unitIdentifierField);
        }

        if (!$classUpdater->hasField('unit')) {
            $classUpdater->insertFieldAfter('digitalProduct', $unitField);
        }

        $classUpdater->save();

        $orderItemClass = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderItemClass);
        if (!$classUpdater->hasField('unitIdentifier')) {
            $classUpdater->insertFieldAfter('digitalProduct', $unitIdentifierField);
        }

        if (!$classUpdater->hasField('unit')) {
            $classUpdater->insertFieldAfter('digitalProduct', $unitField);
        }

        $classUpdater->save();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
