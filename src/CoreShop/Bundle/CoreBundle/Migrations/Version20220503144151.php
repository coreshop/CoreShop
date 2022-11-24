<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20220503144151 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $classUpdater = new ClassUpdate($this->container->getParameter('coreshop.model.product.pimcore_class_name'));

        if ($classUpdater->hasField('attributes')) {
            return;
        }

        $attributesLayout = [
            'fieldtype' => 'panel',
            'layout' => null,
            'border' => false,
            'name' => 'attributes',
            'type' => null,
            'region' => null,
            'title' => 'coreshop.product.attributes',
            'width' => '',
            'height' => '',
            'collapsible' => false,
            'collapsed' => false,
            'bodyStyle' => '',
            'datatype' => 'layout',
            'permissions' => null,
            'childs' => [
                [
                    'fieldtype' => 'coreShopRelation',
                    'stack' => 'coreshop.purchasable',
                    'returnConcrete' => true,
                    'relationType' => true,
                    'objectsAllowed' => true,
                    'assetsAllowed' => false,
                    'documentsAllowed' => false,
                    'width' => null,
                    'assetUploadPath' => null,
                    'assetTypes' => [],
                    'documentTypes' => [],
                    'classes' => [
                        [
                            'classes' => 'CoreShopProduct',
                        ],
                    ],
                    'pathFormatterClass' => '',
                    'name' => 'mainVariant',
                    'title' => 'Main Variant',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => false,
                    'index' => false,
                    'locked' => false,
                    'style' => '',
                    'permissions' => null,
                    'datatype' => 'data',
                    'invisible' => true,
                    'visibleGridView' => false,
                    'visibleSearch' => false,
                ],
                [
                    'fieldtype' => 'coreShopRelations',
                    'stack' => 'coreshop.attribute_group',
                    'relationType' => true,
                    'objectsAllowed' => true,
                    'assetsAllowed' => false,
                    'documentsAllowed' => false,
                    'width' => null,
                    'height' => '',
                    'maxItems' => '',
                    'assetUploadPath' => null,
                    'assetTypes' => [],
                    'documentTypes' => [],
                    'enableTextSelection' => false,
                    'classes' => [],
                    'pathFormatterClass' => '',
                    'name' => 'allowedAttributeGroups',
                    'title' => 'coreshop.product.allowed_attribute_groups',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => false,
                    'index' => false,
                    'locked' => false,
                    'style' => '',
                    'permissions' => null,
                    'datatype' => 'data',
                    'invisible' => false,
                    'visibleGridView' => false,
                    'visibleSearch' => false,
                ],
                [
                    'fieldtype' => 'coreShopRelations',
                    'stack' => 'coreshop.attribute',
                    'relationType' => true,
                    'objectsAllowed' => true,
                    'assetsAllowed' => false,
                    'documentsAllowed' => false,
                    'width' => null,
                    'height' => '',
                    'maxItems' => '',
                    'assetUploadPath' => null,
                    'assetTypes' => [],
                    'documentTypes' => [],
                    'enableTextSelection' => false,
                    'classes' => [],
                    'pathFormatterClass' => '',
                    'name' => 'attributes',
                    'title' => 'coreshop.product.attributes',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => false,
                    'index' => false,
                    'locked' => false,
                    'style' => '',
                    'permissions' => null,
                    'datatype' => 'data',
                    'invisible' => false,
                    'visibleGridView' => false,
                    'visibleSearch' => false,
                ],
            ],
            'locked' => false,
            'icon' => '',
            'labelWidth' => 0,
            'labelAlign' => 'left',
        ];

        $classUpdater->insertLayoutAfter('details', $attributesLayout);
        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
