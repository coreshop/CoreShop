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

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Resource\Factory\RepositoryFactory;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class DoctrineORMDriver extends AbstractDoctrineDriver
{
    public function getType(): string
    {
        return CoreShopResourceBundle::DRIVER_DOCTRINE_ORM;
    }

    public function load(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        parent::load($container, $metadata);

        $this->addRepositoryFactory($container, $metadata);
    }

    protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $repositoryClassParameterName = sprintf('%s.repository.%s.class', $metadata->getApplicationName(), $metadata->getName());
        $repositoryClass = EntityRepository::class;

        if ($container->hasParameter($repositoryClassParameterName)) {
            $repositoryClass = $container->getParameter($repositoryClassParameterName);
        }

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $definition = new Definition($repositoryClass);
        $definition->setPublic(true);
        $definition->setArguments([
            new Reference($metadata->getServiceId('manager')),
            $this->getClassMetadataDefinition($metadata),
        ]);

        $container->setDefinition($metadata->getServiceId('repository'), $definition);

        if (method_exists($container, 'registerAliasForArgument')) {
            foreach (class_implements($repositoryClass) as $typehintClass) {
                $container->registerAliasForArgument(
                    $metadata->getServiceId('repository'),
                    $typehintClass,
                    $metadata->getHumanizedName() . ' repository'
                );
            }
        }
    }

    protected function addRepositoryFactory(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $repositoryFactoryClassParameterName = sprintf('%s.repository.factory.%s.class', $metadata->getApplicationName(), $metadata->getName());
        $repositoryFactoryClass = RepositoryFactory::class;
        $repositoryClass = EntityRepository::class;

        if ($container->hasParameter($repositoryFactoryClassParameterName)) {
            $repositoryFactoryClass = $container->getParameter($repositoryFactoryClassParameterName);
        }

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $definition = new Definition($repositoryFactoryClass);
        $definition->setPublic(true);
        $definition->setArguments([
            $metadata->getClass('model'),
            $repositoryClass
        ]);

        $container->setDefinition($metadata->getServiceId('repository.factory'), $definition);

        if (method_exists($container, 'registerAliasForArgument')) {
            foreach (class_implements($repositoryClass) as $typehintClass) {
                $container->registerAliasForArgument(
                    $metadata->getServiceId('repository.factory'),
                    $typehintClass,
                    $metadata->getHumanizedName() . ' repository factory'
                );
            }
        }
    }

    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        parent::addManager($container, $metadata);

        if (method_exists($container, 'registerAliasForArgument')) {
            $container->registerAliasForArgument(
                $metadata->getServiceId('manager'),
                EntityManagerInterface::class,
                $metadata->getHumanizedName() . ' manager'
            );
        }
    }

    protected function getManagerServiceId(MetadataInterface $metadata): string
    {
        if ($objectManagerName = $this->getObjectManagerName($metadata)) {
            return sprintf('doctrine.orm.%s_entity_manager', $objectManagerName);
        }

        return 'doctrine.orm.entity_manager';
    }

    protected function getClassMetadataClassname(): string
    {
        return ClassMetadata::class;
    }
}
