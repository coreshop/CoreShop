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

class Version20200415152858 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $orderShipmentItemClassName = $this->container->getParameter('coreshop.model.order_shipment_item.pimcore_class_name');

        $classUpdater = new ClassUpdate($orderShipmentItemClassName);

        $fields = [
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedTotalNet',
                'title' => 'coreshop.order_shipment_item.converted_total_net',
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
                'visibleSearch' => false
            ],
            [
                'fieldtype' => 'coreShopMoney',
                'width' => '',
                'defaultValue' => null,
                'minValue' => null,
                'maxValue' => null,
                'name' => 'convertedTotalGross',
                'title' => 'coreshop.order_shipment_item.converted_total_gross',
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
                'visibleSearch' => false
            ],
        ];

        $save = false;
        $fieldBefore = 'totalGross';

        foreach ($fields as $field) {
            if ($classUpdater->hasField($field['name'])) {
                $fieldBefore = $field['name'];

                continue;
            }

            $classUpdater->insertFieldAfter($fieldBefore, $field);

            $save = true;
            $fieldBefore = $field['name'];
        }

        if ($save) {
            $classUpdater->save();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
