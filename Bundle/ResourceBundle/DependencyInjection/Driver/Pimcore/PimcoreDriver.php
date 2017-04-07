<?php

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Pimcore;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\AbstractDriver;
use CoreShop\Bundle\ResourceBundle\Repository\PimcoreRepository;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class PimcoreDriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return CoreShopResourceBundle::DRIVER_DOCTRINE_ORM;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, MetadataInterface $metadata)
    {
        parent::load($container, $metadata);

        if ($metadata->hasClass('pimcore_controller')) {
            $this->addPimcoreController($container, $metadata);
        }
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function addPimcoreController(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $definition = new Definition($metadata->getClass('pimcore_controller'));
        $definition
            ->setArguments([
                $this->getMetadataDefinition($metadata),
                new Reference($metadata->getServiceId('repository')),
                new Reference($metadata->getServiceId('factory')),
                new Reference('coreshop.resource_controller.event_dispatcher'),
                new Reference('coreshop.resource_controller.form_factory'),
            ])
            ->addMethodCall('setContainer', [new Reference('service_container')])
        ;

        $container->setDefinition($metadata->getServiceId('pimcore_controller'), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $repositoryClassParameterName = sprintf('%s.repository.%s.class', $metadata->getApplicationName(), $metadata->getName());
        $repositoryClass = PimcoreRepository::class;

        if ($container->hasParameter($repositoryClassParameterName)) {
            $repositoryClass = $container->getParameter($repositoryClassParameterName);
        }

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $definition = new Definition($repositoryClass);
        $definition->setArguments([
            $this->getMetadataDefinition($metadata),
        ]);

        $container->setDefinition($metadata->getServiceId('repository'), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata)
    {
        //No Manager needed for Pimcore
    }
}
