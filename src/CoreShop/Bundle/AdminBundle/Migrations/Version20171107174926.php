<?php

namespace CoreShop\Bundle\AdminBundle\Migrations;

use CoreShop\Component\Pimcore\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171107174926 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $classes = [
            'order_item',
            'quote_item'
        ];

        foreach ($classes as $class) {
            $className = str_replace('Pimcore\\Model\\DataObject\\', '', $this->container->getParameter(sprintf('coreshop.model.%s.class', $class)));

            $newField = [
                "fieldtype" => "localizedfields",
                "phpdocType" => "\\Pimcore\\Model\\DataObject\\Localizedfield",
                "name" => "localizedfields",
                "region" => null,
                "layout" => null,
                "title" => null,
                "width" => "",
                "height" => "",
                "maxTabs" => null,
                "labelWidth" => null,
                "hideLabelsWhenTabsReached" => null,
                "fieldDefinitionsCache" => null,
                "tooltip" => "",
                "mandatory" => false,
                "noteditable" => false,
                "index" => null,
                "locked" => false,
                "style" => "",
                "permissions" => null,
                "datatype" => "data",
                "columnType" => null,
                "queryColumnType" => null,
                "relationType" => false,
                "invisible" => false,
                "visibleGridView" => true,
                "visibleSearch" => true,
                "childs" => [
                    "fieldtype" => "input",
                    "width" => null,
                    "queryColumnType" => "varchar",
                    "columnType" => "varchar",
                    "columnLength" => 190,
                    "phpdocType" => "string",
                    "regex" => "",
                    "unique" => false,
                    "name" => "name",
                    "title" => "Name",
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
            ];

            $classUpdater = new ClassUpdate($className);

            if (!$classUpdater->hasField('name')) {
                $classUpdater->insertFieldBefore('isGiftItem', $newField);
                $classUpdater->save();
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
