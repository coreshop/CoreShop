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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\AbstractDriver;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDoctrineDriver extends AbstractDriver
{
    protected function getClassMetadataDefinition(MetadataInterface $metadata): Definition
    {
        $definition = new Definition($this->getClassMetadataClassname());
        $definition
            ->setFactory([new Reference($this->getManagerServiceId($metadata)), 'getClassMetadata'])
            ->setArguments([$metadata->getClass('model')])
            ->setPublic(false)
        ;

        return $definition;
    }

    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $alias = new Alias($this->getManagerServiceId($metadata));
        $alias->setPublic(true);

        $container->setAlias(
            $metadata->getServiceId('manager'),
            $alias,
        );

        $container->registerAliasForArgument(
            $metadata->getServiceId('manager'),
            ObjectManager::class,
            $metadata->getHumanizedName() . ' manager',
        );
    }

    protected function getObjectManagerName(MetadataInterface $metadata): ?string
    {
        $objectManagerName = null;

        if ($metadata->hasParameter('options') && isset($metadata->getParameter('options')['object_manager'])) {
            $objectManagerName = $metadata->getParameter('options')['object_manager'];
        }

        return $objectManagerName;
    }

    abstract protected function getManagerServiceId(MetadataInterface $metadata): string;

    abstract protected function getClassMetadataClassname(): string;
}
