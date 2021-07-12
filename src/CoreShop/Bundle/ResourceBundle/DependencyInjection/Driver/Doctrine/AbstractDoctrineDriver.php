<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
            ->setPublic(false);

        return $definition;
    }

    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $alias = new Alias($this->getManagerServiceId($metadata));
        $alias->setPublic(true);

        $container->setAlias(
            $metadata->getServiceId('manager'),
            $alias
        );

        if (method_exists($container, 'registerAliasForArgument')) {
            $container->registerAliasForArgument(
                $metadata->getServiceId('manager'),
                ObjectManager::class,
                $metadata->getHumanizedName() . ' manager'
            );
        }
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
