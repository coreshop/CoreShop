<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180726131622 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $manuallyCreatedField = [
            "fieldtype" => "checkbox",
            "defaultValue" => 0,
            "queryColumnType" => "tinyint(1)",
            "columnType" => "tinyint(1)",
            "phpdocType" => "boolean",
            "name" => "manuallyCreated",
            "title" => "Manually Created",
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

        $order = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($order);

        if (!$classUpdater->hasField('manuallyCreated')) {
            $classUpdater->insertFieldAfter('orderNumber', $manuallyCreatedField);
            $classUpdater->save();
        }

        $quote = $this->container->getParameter('coreshop.model.quote.pimcore_class_name');
        $classUpdater = new ClassUpdate($quote);

        if (!$classUpdater->hasField('manuallyCreated')) {
            $classUpdater->insertFieldAfter('quoteNumber', $manuallyCreatedField);
            $classUpdater->save();
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
