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

final class Version20221118072600 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $classUpdater = new ClassUpdate(
            $this->container->getParameter('coreshop.model.wishlist_item.pimcore_class_name'),
        );

        if ($classUpdater->hasField('wishlist')) {
            return;
        }

        $orderField = [
            'name' => 'wishlist',
            'title' => 'coreshop.wishlist_item.wishlist',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'fieldtype' => 'coreShopRelation',
            'relationType' => true,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
            'classes' => [
                [
                    'classes' => 'CoreShopWishlist',
                ],
            ],
            'pathFormatterClass' => '',
            'width' => null,
            'assetUploadPath' => null,
            'objectsAllowed' => true,
            'assetsAllowed' => false,
            'assetTypes' => [],
            'documentsAllowed' => false,
            'documentTypes' => [],
            'stack' => 'coreshop.wishlist',
            'returnConcrete' => false,
        ];

        $classUpdater->insertFieldAfter('quantity', $orderField);
        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
    }
}
