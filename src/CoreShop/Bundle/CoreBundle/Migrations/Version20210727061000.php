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

final class Version20210727061000 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $fieldDef = [
            "fieldtype" => "panel",
            "layout" => null,
            "border" => false,
            "name" => "units",
            "type" => null,
            "region" => null,
            "title" => "coreshop.order_item.units",
            "width" => "",
            "height" => "",
            "collapsible" => false,
            "collapsed" => false,
            "bodyStyle" => "",
            "datatype" => "layout",
            "permissions" => null,
            "childs" => [
                [
                    "fieldtype" => "coreShopRelations",
                    "stack" => "coreshop.order_item_unit",
                    "relationType" => true,
                    "objectsAllowed" => true,
                    "assetsAllowed" => false,
                    "documentsAllowed" => false,
                    "width" => null,
                    "height" => "",
                    "maxItems" => "",
                    "assetUploadPath" => null,
                    "assetTypes" => [],
                    "documentTypes" => [],
                    "classes" => [],
                    "pathFormatterClass" => "",
                    "name" => "units",
                    "title" => "coreshop.order_item.units",
                    "tooltip" => "",
                    "mandatory" => false,
                    "noteditable" => true,
                    "index" => false,
                    "locked" => false,
                    "style" => "",
                    "permissions" => null,
                    "datatype" => "data",
                    "invisible" => false,
                    "visibleGridView" => false,
                    "visibleSearch" => false,
                ],
            ],
            "locked" => false,
            "icon" => "",
            "labelWidth" => "",
            "labelAlign" => "left",
        ];

        $orderItemClassName = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $classUpdate = new ClassUpdate($orderItemClassName);

        if ($classUpdate->hasField('units')) {
            return;
        }

        $classUpdate->insertLayoutAfter('numbers', $fieldDef);
        $classUpdate->save();
    }

    public function down(Schema $schema): void
    {

    }
}
