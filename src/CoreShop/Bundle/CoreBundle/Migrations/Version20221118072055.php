<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20221118072055 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $classUpdater = new ClassUpdate($this->container->getParameter('coreshop.model.order_item.pimcore_class_name'));

        if ($classUpdater->hasField('order')) {
            return;
        }

        $orderField = [
            "name" => "order",
            "title" => "coreshop.order_item.order",
            "tooltip" => "",
            "mandatory" => false,
            "noteditable" => false,
            "index" => false,
            "locked" => false,
            "style" => "",
            "permissions" => null,
            "datatype" => "data",
            "fieldtype" => "coreShopRelation",
            "relationType" => true,
            "invisible" => false,
            "visibleGridView" => false,
            "visibleSearch" => false,
            "classes" => [
                [
                    "classes" => "CoreShopOrder",
                ],
            ],
            "pathFormatterClass" => "",
            "width" => null,
            "assetUploadPath" => null,
            "objectsAllowed" => true,
            "assetsAllowed" => false,
            "assetTypes" => [],
            "documentsAllowed" => false,
            "documentTypes" => [],
            "stack" => "coreshop.order",
            "returnConcrete" => false,
        ];

        $classUpdater->insertFieldAfter('mainObjectId', $orderField);
        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {

    }
}
