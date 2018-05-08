<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180508095520 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $field = [
            "fieldtype" => "textarea",
            "width" => "",
            "height" => "",
            "queryColumnType" => "longtext",
            "columnType" => "longtext",
            "phpdocType" => "string",
            "name" => "description",
            "title" => "Description",
            "tooltip" => "",
            "mandatory" => false,
            "noteditable" => false,
            "index" => false,
            "locked" => false,
            "style" => "",
            "permissions" => null,
            "datatype" => "data",
            "relationType" => false,
            "invisible" => false,
            "visibleGridView" => false,
            "visibleSearch" => false
        ];

        $categoryClass = $this->container->getParameter('coreshop.model.category.pimcore_class_name');

        $classUpdater = new ClassUpdate($categoryClass);

        if (!$classUpdater->hasField('description')) {
            $classUpdater->insertFieldAfter('name', $field);
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
