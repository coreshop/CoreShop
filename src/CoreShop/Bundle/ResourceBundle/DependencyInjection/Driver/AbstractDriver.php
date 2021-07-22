<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver;

use CoreShop\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Resource\Factory\TranslatableFactoryInterface;
use CoreShop\Component\Resource\Metadata\Metadata;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDriver implements DriverInterface
{
    public function load(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $this->setClassesParameters($container, $metadata);

        if ($metadata->hasClass('admin_controller')) {
            $this->addController($container, $metadata);
        }

        $this->addManager($container, $metadata);
        $this->addRepository($container, $metadata);

        if ($metadata->hasClass('factory')) {
            $this->addFactory($container, $metadata);
        }
    }

    protected function setClassesParameters(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        if ($metadata->hasClass('model')) {
            $container->setParameter(sprintf('%s.model.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('model'));
        }
        if ($metadata->hasClass('admin_controller')) {
            $container->setParameter(sprintf('%s.admin_controller.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('admin_controller'));
        }
        if ($metadata->hasClass('factory')) {
            $container->setParameter(sprintf('%s.factory.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('factory'));
        }
        if ($metadata->hasClass('repository')) {
            $container->setParameter(sprintf('%s.repository.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('repository'));
        }
    }

    protected function addController(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $definition = new Definition($metadata->getClass('admin_controller'));
        $definition
            ->setPublic(true)
            ->setArguments([
                $this->getMetadataDefinition($metadata),
                new Reference($metadata->getServiceId('repository')),
                new Reference($metadata->getServiceId('factory')),
                new Reference($metadata->getServiceId('manager')),
                new Reference(ViewHandlerInterface::class),
                new Reference(EventDispatcherInterface::class),
                new Reference(ResourceFormFactoryInterface::class),
                new Reference(ErrorSerializer::class),
            ])
            ->addMethodCall('setContainer', [new Reference('service_container')]);

        $container->setDefinition($metadata->getServiceId('admin_controller'), $definition);
    }

    protected function addFactory(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $factoryClass = $metadata->getClass('factory');
        $modelClass = $metadata->getClass('model');

        $definition = new Definition($factoryClass);
        $definition->setPublic(true);

        $definitionArgs = [$modelClass];
        if (in_array(TranslatableFactoryInterface::class, class_implements($factoryClass))) {
            $decoratedDefinition = new Definition(Factory::class);
            $decoratedDefinition->setArguments($definitionArgs);

            $definitionArgs = [$decoratedDefinition, new Reference('coreshop.translation_locale_provider')];
        }

        $definition->setArguments($definitionArgs);

        $container->setDefinition($metadata->getServiceId('factory'), $definition);

        if (method_exists($container, 'registerAliasForArgument')) {
            foreach (class_implements($factoryClass) as $typehintClass) {
                $container->registerAliasForArgument(
                    $metadata->getServiceId('factory'),
                    $typehintClass,
                    $metadata->getHumanizedName() . ' factory'
                );
            }
        }
    }

    protected function getMetadataDefinition(MetadataInterface $metadata): Definition
    {
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([new Reference(RegistryInterface::class), 'get'])
            ->setArguments([$metadata->getAlias()]);

        return $definition;
    }

    abstract protected function addManager(ContainerBuilder $container, MetadataInterface $metadata): void;

    abstract protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata): void;
}
