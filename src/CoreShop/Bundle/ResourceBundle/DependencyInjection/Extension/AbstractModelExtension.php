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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\DriverProvider;
use CoreShop\Component\Resource\Metadata\Metadata;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

abstract class AbstractModelExtension extends Extension
{
    /**
     * @param string           $applicationName
     * @param string           $driver
     * @param array            $resources
     * @param ContainerBuilder $container
     */
    protected function registerResources($applicationName, $driver, array $resources, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), $driver), true);
        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $driver);

        foreach ($resources as $resourceName => $resourceConfig) {
            $alias = $applicationName.'.'.$resourceName;
            $resourceConfig = array_merge(['driver' => $driver], $resourceConfig);

            $resources = $container->hasParameter('coreshop.resources') ? $container->getParameter('coreshop.resources') : [];
            $resources = array_merge($resources, [$alias => $resourceConfig]);
            $container->setParameter('coreshop.resources', $resources);

            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            DriverProvider::get($metadata)->load($container, $metadata);

            if ($metadata->hasParameter('translation')) {
                $alias = $alias.'_translation';
                $resourceConfig = array_merge(['driver' => $driver], $resourceConfig['translation']);

                $resources = $container->hasParameter('coreshop.resources') ? $container->getParameter('coreshop.resources') : [];
                $resources = array_merge($resources, [$alias => $resourceConfig]);
                $container->setParameter('coreshop.resources', $resources);

                $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

                DriverProvider::get($metadata)->load($container, $metadata);
            }
        }
    }

    protected function registerPimcoreModels($applicationName, array $models, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), 'pimcore'), true);
        $container->setParameter(sprintf('%s.driver', $this->getAlias()), 'pimcore');

        foreach ($models as $modelName => $modelConfig) {
            $alias = $applicationName.'.'.$modelName;
            $modelConfig = array_merge(['driver' => 'pimcore'], $modelConfig);

            $models = $container->hasParameter('coreshop.pimcore') ? $container->getParameter('coreshop.pimcore') : [];
            $models = array_merge($models, [$alias => $modelConfig]);
            $container->setParameter('coreshop.pimcore', $models);

            $metadata = Metadata::fromAliasAndConfiguration($alias, $modelConfig);

            DriverProvider::get($metadata)->load($container, $metadata);
        }
    }

    protected function registerPimcoreResources($applicationName, $bundleResources, ContainerBuilder $container) {
        $resourceTypes = ['js', 'css'];

        foreach ($resourceTypes as $resourceType) {
            if (array_key_exists($resourceType, $bundleResources)) {
                $applicationParameter = sprintf('%s.pimcore.admin.%s', $applicationName, $resourceType);
                $aliasParameter = sprintf('%s.pimcore.admin.%s', $this->getAlias(), $resourceType);
                $globalParameter = sprintf('resources.admin.%s', $resourceType);

                $parameters = [
                    $applicationParameter, $aliasParameter, $globalParameter
                ];

                foreach ($parameters as $containerParameter) {
                    $resources = [];

                    if ($container->hasParameter($containerParameter)) {
                        $resources = $container->getParameter($containerParameter);
                    }

                    $container->setParameter($containerParameter, array_merge($resources, array_values($bundleResources[$resourceType])));
                }
            }
        }

        if (array_key_exists('permissions', $bundleResources)) {
            $applicationPermissions = [];
            $applicationParameter = sprintf('%s.permissions', $applicationName);
            $resourcePermissions = [];
            $globalParameter = sprintf('coreshop.resource.permissions', $applicationName);

            if ($container->hasParameter($applicationParameter)) {
                $applicationPermissions = $container->getParameter($applicationParameter);
            }

            if ($container->hasParameter($globalParameter)) {
                $resourcePermissions = $container->getParameter($globalParameter);
            }

            $permissions = [];

            foreach ($bundleResources['permissions'] as $permission) {
                $identifier = sprintf('%s_permission_%s', $applicationName, $permission);

                $permissions[] = $identifier;
                $resourcePermissions[] = $identifier;
            }

            $container->setParameter($globalParameter, array_merge($applicationPermissions, $permissions));
            $container->setParameter($applicationParameter, array_merge($applicationPermissions, $permissions));
        }
    }
}
