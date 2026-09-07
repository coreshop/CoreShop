<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Extension\AbstractPimcoreExtension;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\DriverProvider;
use CoreShop\Component\Resource\Metadata\Metadata;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractModelExtension extends AbstractPimcoreExtension
{
    /**
     * @param string           $applicationName
     * @param string           $driver
     */
    protected function registerResources($applicationName, $driver, array $resources, ContainerBuilder $container): void
    {
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), $driver), true);
        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $driver);

        foreach ($resources as $resourceName => $resourceConfig) {
            $alias = $applicationName . '.' . $resourceName;
            $resourceConfig = array_merge(['driver' => $driver], $resourceConfig);

            $resources = $container->hasParameter('coreshop.resources') ? $container->getParameter('coreshop.resources') : [];
            $resources = array_merge($resources, [$alias => $resourceConfig]);
            $container->setParameter('coreshop.resources', $resources);

            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            DriverProvider::get($metadata)->load($container, $metadata);

            if ($metadata->hasParameter('translation')) {
                $alias .= '_translation';
                $resourceConfig = array_merge(['driver' => $driver], $resourceConfig['translation']);

                $resources = $container->hasParameter('coreshop.resources') ? $container->getParameter('coreshop.resources') : [];
                $resources = array_merge($resources, [$alias => $resourceConfig]);
                $container->setParameter('coreshop.resources', $resources);

                $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

                DriverProvider::get($metadata)->load($container, $metadata);
            }
        }
    }

    /**
     * @param string           $applicationName
     */
    protected function registerPimcoreModels($applicationName, array $models, ContainerBuilder $container): void
    {
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), 'pimcore'), true);
        $container->setParameter(sprintf('%s.driver', $this->getAlias()), 'pimcore');

        foreach ($models as $modelName => $modelConfig) {
            $alias = $applicationName . '.' . $modelName;
            $modelConfig = array_merge(['driver' => 'pimcore', 'alias' => $this->getAlias()], $modelConfig);

            $modelConfig['pimcore_class'] = match ($modelConfig['classes']['type']) {
                CoreShopResourceBundle::PIMCORE_MODEL_TYPE_FIELD_COLLECTION => str_replace(
                    'Pimcore\Model\DataObject\Fieldcollection\Data\\',
                    '',
                    $modelConfig['classes']['model'],
                ),
                CoreShopResourceBundle::PIMCORE_MODEL_TYPE_BRICK => str_replace(
                    'Pimcore\Model\DataObject\Objectbrick\Data\\',
                    '',
                    $modelConfig['classes']['model'],
                ),
                default => str_replace(
                    'Pimcore\Model\DataObject\\',
                    '',
                    $modelConfig['classes']['model'],
                ),
            };

            foreach (['coreshop.all.pimcore_classes', sprintf('%s.pimcore_classes', $applicationName)] as $parameter) {
                $models = $container->hasParameter($parameter) ? $container->getParameter($parameter) : [];
                $models = array_merge($models, [$alias => $modelConfig]);
                $container->setParameter($parameter, $models);
            }

            $metadata = Metadata::fromAliasAndConfiguration($alias, $modelConfig);

            DriverProvider::get($metadata)->load($container, $metadata);
        }
    }

    public function registerStack(string $applicationName, array $stack, ContainerBuilder $container): void
    {
        $appParameterName = sprintf('%s.stack', $applicationName);
        $globalParameterName = 'coreshop.all.stack';

        foreach ([$appParameterName, $globalParameterName] as $parameterName) {
            /**
             * @var array $stackConfig
             */
            $stackConfig = $container->hasParameter($parameterName) ? $container->getParameter($parameterName) : [];

            foreach ($stack as $key => $interface) {
                $key = sprintf('%s.%s', $applicationName, $key);

                if (array_key_exists($key, $stackConfig)) {
                    throw new \RuntimeException(sprintf('Stack Key %s found twice', $key));
                }

                $stackConfig[$key] = $interface;
            }

            $container->setParameter($parameterName, $stackConfig);
        }
    }

    /**
     * @param string           $applicationName
     * @param array            $bundles
     */
    public function registerDependantBundles($applicationName, $bundles, ContainerBuilder $container): void
    {
        $appParameterName = sprintf('%s.dependant.bundles', $applicationName);
        $globalParameterName = 'coreshop.all.dependant.bundles';

        foreach ([$appParameterName, $globalParameterName] as $parameterName) {
            /**
             * @var array $bundleConfig
             */
            $bundleConfig = $container->hasParameter($parameterName) ? $container->getParameter($parameterName) : [];

            foreach ($bundles as $bundleName) {
                $bundleConfig[] = $bundleName;
            }

            $container->setParameter($parameterName, $bundleConfig);
        }
    }
}
