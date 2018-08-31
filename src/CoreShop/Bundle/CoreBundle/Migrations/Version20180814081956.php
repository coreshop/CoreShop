<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180814081956 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $fields = [
            "itemTotalGross" => [
                "fieldtype" => "coreShopMoney",
                "width" => "",
                "defaultValue" => null,
                "queryColumnType" => "bigint(20)",
                "columnType" => "bigint(20)",
                "phpdocType" => "int",
                "minValue" => null,
                "maxValue" => null,
                "name" => "totalGross",
                "title" => "Total Gross",
                "tooltip" => "",
                "mandatory" => false,
                "noteditable" => true,
                "index" => false,
                "locked" => false,
                "style" => "",
                "permissions" => null,
                "datatype" => "data",
                "relationType" => false,
                "invisible" => false,
                "visibleGridView" => false,
                "visibleSearch" => false,
            ],
            "itemTotalNet" => [
                "fieldtype" => "coreShopMoney",
                "width" => "",
                "defaultValue" => null,
                "queryColumnType" => "bigint(20)",
                "columnType" => "bigint(20)",
                "phpdocType" => "int",
                "minValue" => null,
                "maxValue" => null,
                "name" => "totalNet",
                "title" => "Total Net",
                "tooltip" => "",
                "mandatory" => false,
                "noteditable" => true,
                "index" => false,
                "locked" => false,
                "style" => "",
                "permissions" => null,
                "datatype" => "data",
                "relationType" => false,
                "invisible" => false,
                "visibleGridView" => false,
                "visibleSearch" => false,
            ],
        ];

        $cartItemClass = $this->container->getParameter('coreshop.model.cart_item.pimcore_class_name');

        $cartItemClassUpdater = new ClassUpdate($cartItemClass);

        foreach ($fields as $key => $field) {
            if (!$cartItemClassUpdater->hasField($key)) {
                $cartItemClassUpdater->insertFieldAfter('itemPriceNet', $field);
            }
        }

        $cartItemClassUpdater->save();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
