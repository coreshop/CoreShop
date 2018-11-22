<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Pimcore;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\AbstractDriver;
use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\Alias;
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
            if (is_array($metadata->getClass('pimcore_controller'))) {
                foreach ($metadata->getClass('pimcore_controller') as $suffix => $class) {
                    $this->addPimcoreController($container, $metadata, $class, $suffix);
                }
            } else {
                $this->addDefaultPimcoreController($container, $metadata);
            }
        }

        if ($metadata->hasParameter('path')) {
            $this->addPimcoreClass($container, $metadata);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setClassesParameters(ContainerBuilder $container, MetadataInterface $metadata)
    {
        parent::setClassesParameters($container, $metadata);

        if ($metadata->hasParameter('pimcore_class')) {
            $container->setParameter(sprintf('%s.model.%s.pimcore_class_name', $metadata->getApplicationName(), $metadata->getName()), $metadata->getParameter('pimcore_class'));
        }
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function addDefaultPimcoreController(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $this->addPimcoreController($container, $metadata, $metadata->getClass('pimcore_controller'));
    }

    protected function addPimcoreController(ContainerBuilder $container, MetadataInterface $metadata, $classValue, $suffix = null)
    {
        $definition = new Definition($classValue);
        $definition
            ->setPublic(true)
            ->setArguments([
                $this->getMetadataDefinition($metadata),
                new Reference($metadata->getServiceId('repository')),
                new Reference($metadata->getServiceId('factory')),
                new Reference('coreshop.resource_controller.view_handler'),
            ])
            ->addMethodCall('setContainer', [new Reference('service_container')]);

        $serviceId = $metadata->getServiceId('pimcore_controller');

        if (null !== $suffix && 'default' !== $suffix) {
            $serviceId .= '_' . $suffix;
        }

        $container->setDefinition($serviceId, $definition);
    }

    /**
     * @param ContainerBuilder  $container
     * @param MetadataInterface $metadata
     */
    protected function addPimcoreClass(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $folder = $metadata->getParameter('path');

        if (!is_array($folder)) {
            $folders[$metadata->getName()] = $folder;
        } else {
            $folders = $folder;
        }

        $parameterNameForAllAppPaths = sprintf('%s.folders', $metadata->getApplicationName());
        $parameterNameForAllPaths = 'coreshop.resource.folders';

        foreach ($folders as $folderType => $folder) {
            $paramName = sprintf('%s.folder.%s', $metadata->getApplicationName(), $folderType);
            $container->setParameter($paramName, $folder);

            foreach ([$parameterNameForAllPaths, $parameterNameForAllAppPaths] as $parameterName) {
                $allPaths = [];

                if ($container->hasParameter($parameterName)) {
                    $allPaths = $container->getParameter($parameterName);
                }

                $allPaths[$paramName] = $folder;

                $container->setParameter($parameterName, $allPaths);
            }
        }
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
        $definition->setPublic(true);
        $definition->setArguments([
            $this->getMetadataDefinition($metadata),
        ]);
        $definition->addTag('coreshop.pimcore.repository', ['alias' => $metadata->getAlias()]);

        $container->setDefinition($metadata->getServiceId('repository'), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $alias = new Alias('pimcore.dao.object_manager');
        $alias->setPublic(true);

        $container->setAlias(
            $metadata->getServiceId('manager'),
            $alias
        );
    }
}
