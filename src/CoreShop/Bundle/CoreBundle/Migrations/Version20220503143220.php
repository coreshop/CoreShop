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

use CoreShop\Component\Pimcore\DataObject\ClassInstaller;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\DataObject\CoreShopAttributeColor;
use Pimcore\Model\DataObject\CoreShopAttributeGroup;
use Pimcore\Model\DataObject\CoreShopAttributeValue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20220503143220 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        /**
         * @var ClassInstaller $classInstaller
         */
        $classInstaller = $this->container->get(ClassInstaller::class);

        /**
         * @var \Kernel $kernel
         */
        $kernel = $this->container->get('kernel');

        if (!class_exists(CoreShopAttributeGroup::class)) {
            $classInstaller->createClass($kernel->locateResource('@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeGroup.json'), 'CoreShopAttributeGroup');
        }

        if (!class_exists(CoreShopAttributeColor::class)) {
            $classInstaller->createClass($kernel->locateResource('@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeColor.json'), 'CoreShopAttributeColor');
        }

        if (!class_exists(CoreShopAttributeValue::class)) {
            $classInstaller->createClass($kernel->locateResource('@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeValue.json'), 'CoreShopAttributeValue');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
