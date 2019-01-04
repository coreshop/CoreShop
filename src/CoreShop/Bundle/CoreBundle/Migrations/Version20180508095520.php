<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
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
            'fieldtype' => 'textarea',
            'width' => '',
            'height' => '',
            'queryColumnType' => 'longtext',
            'columnType' => 'longtext',
            'phpdocType' => 'string',
            'name' => 'description',
            'title' => 'Description',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $localizedSeoFields = [
            'fieldtype' => 'localizedfields',
            'phpdocType' => '\\Pimcore\\Model\\DataObject\\Localizedfield',
            'childs' => [
                [
                    'fieldtype' => 'input',
                    'width' => null,
                    'queryColumnType' => 'varchar',
                    'columnType' => 'varchar',
                    'columnLength' => 190,
                    'phpdocType' => 'string',
                    'regex' => '',
                    'unique' => false,
                    'name' => 'pimcoreMetaTitle',
                    'title' => 'Meta Title',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => false,
                    'index' => false,
                    'locked' => false,
                    'style' => '',
                    'permissions' => null,
                    'datatype' => 'data',
                    'relationType' => false,
                    'invisible' => false,
                    'visibleGridView' => true,
                    'visibleSearch' => true,
                ],
                [
                    'fieldtype' => 'textarea',
                    'width' => '',
                    'height' => '',
                    'queryColumnType' => 'longtext',
                    'columnType' => 'longtext',
                    'phpdocType' => 'string',
                    'name' => 'pimcoreMetaDescription',
                    'title' => 'Meta Description',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => false,
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
            ],
            'name' => 'localizedfields',
            'region' => null,
            'layout' => null,
            'title' => '',
            'width' => '',
            'height' => '',
            'maxTabs' => null,
            'labelWidth' => null,
            'hideLabelsWhenTabsReached' => null,
            'fieldDefinitionsCache' => null,
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => null,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'columnType' => null,
            'queryColumnType' => null,
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => true,
            'visibleSearch' => true,
        ];

        $categoryClass = $this->container->getParameter('coreshop.model.category.pimcore_class_name');
        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');

        $categoryClassUpdater = new ClassUpdate($categoryClass);
        $productClassUpdater = new ClassUpdate($productClass);

        if (!$categoryClassUpdater->hasField('description')) {
            $categoryClassUpdater->insertFieldAfter('name', $field);
        }

        if (!$categoryClassUpdater->hasField('pimcoreMetaDescription')) {
            $categoryClassUpdater->insertFieldAfter('parentCategory', $localizedSeoFields);
        }

        if (!$productClassUpdater->hasField('pimcoreMetaDescription')) {
            $productClassUpdater->insertFieldAfter('stores', $localizedSeoFields);
        }

        $categoryClassUpdater->save();
        $productClassUpdater->save();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
