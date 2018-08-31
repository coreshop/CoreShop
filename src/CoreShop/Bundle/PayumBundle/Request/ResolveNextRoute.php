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
