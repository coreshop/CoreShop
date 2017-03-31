<?php

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver;

use CoreShop\Component\Core\Factory\Factory;
use CoreShop\Component\Core\Metadata\Metadata;
use CoreShop\Component\Core\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
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
     * @param ContainerBuilder $container
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
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    protected function addController(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $definition = new Definition($metadata->getClass('admin_controller'));
        $definition
            ->setArguments([
                $this->getMetadataDefinition($metadata),
                new Reference($metadata->getServiceId('repository')),
                new Reference($metadata->getServiceId('factory')),
                new Reference($metadata->getServiceId('manager'))
            ])
            ->addMethodCall('setContainer', [new Reference('service_container')])
        ;

        $container->setDefinition($metadata->getServiceId('admin_controller'), $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    protected function addFactory(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $factoryClass = $metadata->getClass('factory');
        $modelClass = $metadata->getClass('model');

        $definition = new Definition($factoryClass);

        $definitionArgs = [$modelClass];
        /*if (in_array(TranslatableFactoryInterface::class, class_implements($factoryClass))) {
            $decoratedDefinition = new Definition(Factory::class);
            $decoratedDefinition->setArguments($definitionArgs);

            $definitionArgs = [$decoratedDefinition, new Reference('sylius.translation_locale_provider')];
        }*/

        $definition->setArguments($definitionArgs);

        $container->setDefinition($metadata->getServiceId('factory'), $definition);
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
            ->setFactory([new Reference('coreshop.resource_registry'), 'get'])
            ->setArguments([$metadata->getAlias()])
        ;

        return $definition;
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    abstract protected function addManager(ContainerBuilder $container, MetadataInterface $metadata);

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    abstract protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata);
}
