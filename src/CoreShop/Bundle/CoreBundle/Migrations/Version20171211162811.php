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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171211162811 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException
     */
    public function up(Schema $schema)
    {
        $digitalProductMain = [
            'fieldtype' => 'checkbox',
            'defaultValue' => 0,
            'queryColumnType' => 'tinyint(1)',
            'columnType' => 'tinyint(1)',
            'phpdocType' => 'boolean',
            'name' => 'digitalProduct',
            'title' => 'Digital Product',
            'tooltip' => '',
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
        ];

        $digitalItem = [
            'fieldtype' => 'checkbox',
            'defaultValue' => 0,
            'queryColumnType' => 'tinyint(1)',
            'columnType' => 'tinyint(1)',
            'phpdocType' => 'boolean',
            'name' => 'digitalProduct',
            'title' => 'Digital Product',
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

        $product = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $classUpdater = new ClassUpdate($product);
        if (!$classUpdater->hasField('digitalProduct')) {
            $classUpdater->insertFieldAfter('active', $digitalProductMain);
            $classUpdater->save();
        }

        $cartItem = $this->container->getParameter('coreshop.model.cart_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($cartItem);
        if (!$classUpdater->hasField('digitalProduct')) {
            $classUpdater->insertFieldAfter('isGiftItem', $digitalItem);
            $classUpdater->save();
        }

        $orderItem = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderItem);
        if (!$classUpdater->hasField('digitalProduct')) {
            $classUpdater->insertFieldAfter('isGiftItem', $digitalItem);
            $classUpdater->save();
        }

        $quoteItem = $this->container->getParameter('coreshop.model.quote_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($quoteItem);
        if (!$classUpdater->hasField('digitalProduct')) {
            $classUpdater->insertFieldAfter('isGiftItem', $digitalItem);
            $classUpdater->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
