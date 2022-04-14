<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20220414125342 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $orderClassName = $this->container->getParameter('coreshop.model.order_item.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderClassName);

        $convertedTotalGrossField = [
            "fieldtype" => "coreShopMoney",
            "width" => "",
            "defaultValue" => null,
            "phpdocType" => "int",
            "minValue" => null,
            "maxValue" => null,
            "name" => "convertedSubtotalGross",
            "title" => "coreshop.order_item.converted_subtotal_gross",
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
        ];
        $convertedTotalNetField = [
            "fieldtype" => "coreShopMoney",
            "width" => "",
            "defaultValue" => null,
            "phpdocType" => "int",
            "minValue" => null,
            "maxValue" => null,
            "name" => "convertedSubtotalNet",
            "title" => "coreshop.order_item.converted_subtotal_net",
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
        ];
        $subtotalNetField = [
            "fieldtype" => "coreShopMoney",
            "width" => "",
            "defaultValue" => null,
            "phpdocType" => "int",
            "minValue" => null,
            "maxValue" => null,
            "name" => "subtotalNet",
            "title" => "coreshop.order_item.subtotal_net",
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
        ];
        $subtotalGrossField = [
            "fieldtype" => "coreShopMoney",
            "width" => "",
            "defaultValue" => null,
            "phpdocType" => "int",
            "minValue" => null,
            "maxValue" => null,
            "name" => "subtotalGross",
            "title" => "coreshop.order_item.subtotal_gross",
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
        ];

        if (!$classUpdater->hasField('convertedSubtotalGross')) {
            $classUpdater->insertFieldBefore('convertedTotalGross', $convertedTotalGrossField);
        }

        if (!$classUpdater->hasField('convertedSubtotalNet')) {
            $classUpdater->insertFieldBefore('convertedTotalGross', $convertedTotalNetField);
        }

        if (!$classUpdater->hasField('subtotalNet')) {
            $classUpdater->insertFieldBefore('totalNet', $subtotalNetField);
        }

        if (!$classUpdater->hasField('subtotalGross')) {
            $classUpdater->insertFieldBefore('totalNet', $subtotalGrossField);
        }

        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
