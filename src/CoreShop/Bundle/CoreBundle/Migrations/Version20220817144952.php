<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20220817144952 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $classUpdater = new ClassUpdate($this->container->getParameter('coreshop.model.order_item.pimcore_class_name'));

        if ($classUpdater->hasField('price_rules')) {
            return;
        }

        $attributesLayout = [
            "fieldtype" => "panel",
            "labelWidth" => 100,
            "name" => "price_rules",
            "type" => null,
            "region" => null,
            "title" => "coreshop.order.price_rules",
            "width" => null,
            "height" => null,
            "collapsible" => false,
            "collapsed" => false,
            "datatype" => "layout",
            "bodyStyle" => [
                "datatype" => "layout",
                "permissions" => null,
                "childs" => [
                    [
                        "fieldtype" => "fieldcollections",
                        "phpdocType" => "\\Pimcore\\Model\\DataObject\\Fieldcollection",
                        "allowedTypes" => [
                            "CoreShopPriceRuleItem"
                        ],
                        "lazyLoading" => true,
                        "maxItems" => "",
                        "disallowAddRemove" => false,
                        "disallowReorder" => false,
                        "collapsed" => false,
                        "collapsible" => false,
                        "border" => false,
                        "name" => "priceRuleItems",
                        "title" => "coreshop.order.price_rules",
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
                        "visibleSearch" => false
                    ]
                ],
                "locked" => false
            ]
        ];

        $classUpdater->insertLayoutAfter('numbers', $attributesLayout);
        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
