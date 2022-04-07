<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20220330153600 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $orderClassName = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderClassName);

        $fieldQuoteNumber = [
            "fieldtype" => "input",
            "width" => null,
            "queryColumnType" => "varchar",
            "columnType" => "varchar",
            "columnLength" => 190,
            "phpdocType" => "string",
            "regex" => "",
            "unique" => false,
            "showCharCount" => null,
            "name" => "quoteNumber",
            "title" => "coreshop.order.quote_number",
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

        if (!$classUpdater->hasField('quoteNumber')) {
            $classUpdater->insertFieldBefore('orderNumber', $fieldQuoteNumber);
        }

        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
