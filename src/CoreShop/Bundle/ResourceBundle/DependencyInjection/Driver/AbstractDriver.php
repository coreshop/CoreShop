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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver;

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
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, MetadataInterface $metadata)
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

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function setClassesParameters(ContainerBuilder $container, MetadataInterface $metadata)
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

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function addController(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $definition = new Definition($metadata->getClass('admin_controller'));
        $definition
            ->setPublic(true)
            ->setArguments([
                $this->getMetadataDefinition($metadata),
                new Reference($metadata->getServiceId('repository')),
                new Reference($metadata->getServiceId('factory')),
                new Reference($metadata->getServiceId('manager')),
                new Reference('coreshop.resource_controller.view_handler'),
                new Reference('coreshop.resource_controller.event_dispatcher'),
                new Reference('coreshop.resource_controller.form_factory'),
                new Reference('coreshop.resource.helper.form_error_serializer'),
            ])
            ->addMethodCall('setContainer', [new Reference('service_container')]);

        $container->setDefinition($metadata->getServiceId('admin_controller'), $definition);
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function addFactory(ContainerBuilder $container, MetadataInterface $metadata)
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

    /**
     * @param MetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getMetadataDefinition(MetadataInterface $metadata)
    {
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([new Reference(RegistryInterface::class), 'get'])
            ->setArguments([$metadata->getAlias()]);

        return $definition;
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    abstract protected function addManager(ContainerBuilder $container, MetadataInterface $metadata);

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    abstract protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata);
}
