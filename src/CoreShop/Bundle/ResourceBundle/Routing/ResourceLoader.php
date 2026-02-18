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

namespace CoreShop\Bundle\ResourceBundle\Routing;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

final class ResourceLoader extends Loader
{
    public function __construct(
        private RegistryInterface $modelRegistry,
        private RouteFactoryInterface $routeFactory,
        private string $backendRequirement,
        ?string $env = null,
    ) {
        parent::__construct($env);
    }

    public function load($resource, $type = null): RouteCollection
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

        $rootPath = sprintf('/{backend}/%s/%s/', $metadata->getApplicationName(), $metadata->getPluralName());

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

    private function createRoute(MetadataInterface $metadata, array $configuration, $path, $actionName, array $methods, array $options): Route
    {
        $defaults = [
            '_controller' => $metadata->getServiceId('admin_controller') . sprintf('::%sAction', $actionName),
            'backend' => 'admin',
        ];

        $requirements = [
            'backend' => $this->backendRequirement,
        ];

        return $this->routeFactory->createRoute($path, $defaults, $requirements, $options, '', [], $methods);
    }

    private function getRouteName(MetadataInterface $metadata, array $configuration, $actionName): string
    {
        $sectionPrefix = isset($configuration['section']) ? $configuration['section'] . '_' : '';

        return sprintf('%s_%s%s_%s', $metadata->getApplicationName(), $sectionPrefix, $metadata->getName(), $actionName);
    }
}
