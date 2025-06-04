<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Routing;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Yaml\Yaml;

final class ResourceLoader implements LoaderInterface
{
    public function __construct(
        private RegistryInterface $modelRegistry,
        private RouteFactoryInterface $routeFactory,
    ) {
    }

    public function load($resource, $type = null)
    {
        $processor = new Processor();
        $configurationDefinition = new Configuration();

        $configuration = Yaml::parse($resource);
        $configuration = $processor->processConfiguration($configurationDefinition, ['routing' => $configuration]);

        $defaultRoutes = [
            'get' => ['GET'],
            'list' => ['GET'],
            'add' => ['POST'],
            'save' => ['POST'],
            'delete' => ['DELETE'],
        ];
        $routesToGenerate = [];

        if (isset($configuration['clone']) && $configuration['clone']) {
            $defaultRoutes['clone'] = ['POST'];
        }

        if (!empty($configuration['no_default_routes'])) {
            $defaultRoutes = [];
        }

        if (isset($configuration['only']) && is_array($configuration['only']) && count($configuration['only']) > 0) {
            foreach ($defaultRoutes as $key => $method) {
                if (!in_array($key, $configuration['only'])) {
                    unset($defaultRoutes[$key]);
                }
            }
        }

        foreach ($defaultRoutes as $route => $methods) {
            $routesToGenerate[] = [
                'path' => $route,
                'action' => $route,
                'methods' => $methods,
                'options' => [
                    'expose' => $configuration['expose'],
                ],
            ];
        }

        if (!empty($configuration['additional_routes'])) {
            $routesToGenerate = array_merge($routesToGenerate, $configuration['additional_routes']);
        }

        /** @var MetadataInterface $metadata */
        $metadata = $this->modelRegistry->get($configuration['alias']);
        $routes = $this->routeFactory->createRouteCollection();

        //$rootPath = sprintf('/%s/', isset($configuration['path']) ? $configuration['path'] : Urlizer::urlize($metadata->getPluralName()));
        //$identifier = sprintf('{%s}', $configuration['identifier']);

        $rootPath = '/admin/' . $metadata->getApplicationName();
        $rootPath .= '/' . $metadata->getPluralName() . '/';

        foreach ($routesToGenerate as $route) {
            $indexRoute = $this->createRoute($metadata, $configuration, $rootPath . $route['path'], $route['action'], $route['methods'], $route['options'] ?? []);
            $routes->add($this->getRouteName($metadata, $configuration, $route['action']), $indexRoute);
        }

        return $routes;
    }

    public function supports($resource, $type = null): bool
    {
        return 'coreshop.resources' === $type;
    }

    /**
     * @psalm-suppress InvalidReturnType Symfony docblocks are messing with us
     */
    public function getResolver()
    {
        // Intentionally left blank.
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        // Intentionally left blank.
    }

    private function createRoute(MetadataInterface $metadata, array $configuration, $path, $actionName, array $methods, array $options): Route
    {
        $defaults = [
            '_controller' => $metadata->getServiceId('admin_controller') . sprintf('::%sAction', $actionName),
        ];

        return $this->routeFactory->createRoute($path, $defaults, [], $options, '', [], $methods);
    }

    private function getRouteName(MetadataInterface $metadata, array $configuration, $actionName): string
    {
        $sectionPrefix = isset($configuration['section']) ? $configuration['section'] . '_' : '';

        return sprintf('%s_%s%s_%s', $metadata->getApplicationName(), $sectionPrefix, $metadata->getName(), $actionName);
    }
}
