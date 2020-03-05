<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200211125150 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $quote = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($quote);

        if (!$classUpdater->hasField('saleState')) {
            $classUpdater->insertFieldAfter('orderNumber', [
                [
                    'fieldtype' => 'input',
                    'width' => null,
                    'queryColumnType' => 'varchar',
                    'columnType' => 'varchar',
                    'columnLength' => 190,
                    'phpdocType' => 'string',
                    'regex' => '',
                    'unique' => false,
                    'showCharCount' => false,
                    'name' => 'saleState',
                    'title' => 'State',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => true,
                    'index' => false,
                    'locked' => false,
                    'style' => '',
                    'permissions' => null,
                    'datatype' => 'data',
                    'relationType' => false,
                    'invisible' => false,
                    'visibleGridView' => false,
                    'visibleSearch' => false,
                ],
            ]);
        }

        if (!$classUpdater->hasField('needsRecalculation')) {
            $classUpdater->insertFieldAfter('items', [
                [
                    'fieldtype' => 'checkbox',
                    'defaultValue' => 0,
                    'queryColumnType' => 'tinyint(1)',
                    'columnType' => 'tinyint(1)',
                    'phpdocType' => 'boolean',
                    'name' => 'needsRecalculation',
                    'title' => 'Needs Recalculation',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => true,
                    'index' => false,
                    'locked' => false,
                    'style' => '',
                    'permissions' => null,
                    'datatype' => 'data',
                    'relationType' => false,
                    'invisible' => true,
                    'visibleGridView' => false,
                    'visibleSearch' => false,
                ],
            ]);
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
