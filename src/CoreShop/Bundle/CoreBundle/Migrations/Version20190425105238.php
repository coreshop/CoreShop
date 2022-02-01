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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190425105238 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $orderItemClass = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $quoteItemClass = $this->container->getParameter('coreshop.model.quote_item.pimcore_class_name');

        $field = [
            'fieldtype' => 'coreShopMoney',
            'width' => '',
            'defaultValue' => null,
            'minValue' => null,
            'maxValue' => null,
            'name' => 'baseItemWholesalePrice',
            'title' => 'Base Item Wholesale Price',
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

        foreach ([$orderItemClass, $quoteItemClass] as $class) {
            $classUpdater = new ClassUpdate($class);

            if (!$classUpdater->hasField('baseItemWholesalePrice')) {
                $classUpdater->insertFieldBefore('baseItemPriceNet', $field);

                $classUpdater->save();
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
