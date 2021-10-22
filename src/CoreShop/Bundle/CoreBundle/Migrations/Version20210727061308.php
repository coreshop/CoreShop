<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20210727061308 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $slugField = [
            "fieldtype" => "urlSlug",
            "width" => "",
            "domainLabelWidth" => null,
            "action" => "CoreShop\\Bundle\\FrontendBundle\\Controller\\ProductController:detailSlugAction",
            "availableSites" => [],
            "name" => "slug",
            "title" => "coreshop.product.slug",
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

        $classUpdater = new ClassUpdate($this->container->getParameter('coreshop.model.product.pimcore_class_name'));
        $classUpdater->setProperty('linkGeneratorReference', '@CoreShop\Component\Pimcore\Slug\SluggableLinkGenerator');

        if (!$classUpdater->hasField('slug')) {
            $classUpdater->insertFieldAfter('shortDescription', $slugField);
        }

        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
