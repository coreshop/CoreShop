<?php

namespace CoreShop\Bundle\ResourceBundle\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RouteFactory implements RouteFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRouteCollection()
    {
        return new RouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute($path, array $defaults = [], array $requirements = [], array $options = [], $host = '', $schemes = [], $methods = [], $condition = '')
    {
        return new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }
}
