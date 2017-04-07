<?php

namespace CoreShop\Bundle\ResourceBundle\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

interface RouteFactoryInterface
{
    /**
     * @return RouteCollection
     */
    public function createRouteCollection();

    /**
     * @param string       $path         The path pattern to match
     * @param array        $defaults     An array of default parameter values
     * @param array        $requirements An array of requirements for parameters (regexes)
     * @param array        $options      An array of options
     * @param string       $host         The host pattern to match
     * @param string|array $schemes      A required URI scheme or an array of restricted schemes
     * @param string|array $methods      A required HTTP method or an array of restricted methods
     * @param string       $condition    A condition that should evaluate to true for the route to match
     *
     * @return Route
     */
    public function createRoute($path, array $defaults = [], array $requirements = [], array $options = [], $host = '', $schemes = [], $methods = [], $condition = '');
}
