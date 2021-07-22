<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
    /**
     * @var RegistryInterface
     */
    private $modelRegistry;

    /**
     * @var RouteFactoryInterface
     */
    private $routeFactory;

    /**
     * @param RegistryInterface     $modelRegistry
     * @param RouteFactoryInterface $routeFactory
     */
    public function __construct(RegistryInterface $modelRegistry, RouteFactoryInterface $routeFactory)
    {
        $this->modelRegistry = $modelRegistry;
        $this->routeFactory = $routeFactory;
    }

    /**
     * {@inheritdoc}
     */
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
            $indexRoute = $this->createRoute($metadata, $configuration, $rootPath . $route['path'], $route['action'], $route['methods']);
            $routes->add($this->getRouteName($metadata, $configuration, $route['action']), $indexRoute);
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'coreshop.resources' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        // Intentionally left blank.
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        // Intentionally left blank.
    }

    /**
     * @param MetadataInterface $metadata
     * @param array             $configuration
     * @param string            $path
     * @param string            $actionName
     * @param array             $methods
     *
     * @return Route
     */
    private function createRoute(MetadataInterface $metadata, array $configuration, $path, $actionName, array $methods)
    {
        $defaults = [
            '_controller' => $metadata->getServiceId('admin_controller') . sprintf(':%sAction', $actionName),
        ];

        return $this->routeFactory->createRoute($path, $defaults, [], [], '', [], $methods);
    }

    /**
     * @param MetadataInterface $metadata
     * @param array             $configuration
     * @param string            $actionName
     *
     * @return string
     */
    private function getRouteName(MetadataInterface $metadata, array $configuration, $actionName)
    {
        $sectionPrefix = isset($configuration['section']) ? $configuration['section'] . '_' : '';

        return sprintf('%s_%s%s_%s', $metadata->getApplicationName(), $sectionPrefix, $metadata->getName(), $actionName);
    }
}
