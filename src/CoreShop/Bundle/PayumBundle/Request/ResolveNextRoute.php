<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PayumBundle\Request;

use Payum\Core\Request\Generic;

class ResolveNextRoute extends Generic implements ResolveNextRouteInterface
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
    public function setRouteName(string $routeName)
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
