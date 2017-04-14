<?php

namespace CoreShop\Bundle\PayumBundle\Request;

use Payum\Core\Request\Generic;

class ResolveNextRoute extends Generic
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var array
     */
    private $routeParameters = [];

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
    }

    /**
     * @return mixed
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * @param array $parameters
     */
    public function setRouteParameters(array $parameters)
    {
        $this->routeParameters = $parameters;
    }
}
