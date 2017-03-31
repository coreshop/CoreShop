<?php

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\AbstractDriver;
use CoreShop\Component\Core\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDoctrineDriver extends AbstractDriver
{
    /**
     * @param MetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getClassMetadataDefinition(MetadataInterface $metadata)
    {
        $definition = new Definition($this->getClassMetadataClassname());
        $definition
            ->setFactory([new Reference($this->getManagerServiceId($metadata)), 'getClassMetadata'])
            ->setArguments([$metadata->getClass('model')])
            ->setPublic(false)
        ;

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $container->setAlias(
            $metadata->getServiceId('manager'),
            new Alias($this->getManagerServiceId($metadata))
        );
    }

    /**
     * Return the configured object managre name, or NULL if the default
     * manager should be used.
     *
     * @param MetadataInterface $metadata
     *
     * @return string|null
     */
    protected function getObjectManagerName(MetadataInterface $metadata)
    {
        $objectManagerName = null;

        if ($metadata->hasParameter('options') && isset($metadata->getParameter('options')['object_manager'])) {
            $objectManagerName = $metadata->getParameter('options')['object_manager'];
        }

        return $objectManagerName;
    }

    /**
     * @param MetadataInterface $metadata
     *
     * @return string
     */
    abstract protected function getManagerServiceId(MetadataInterface $metadata);

    /**
     * @return string
     */
    abstract protected function getClassMetadataClassname();
}
