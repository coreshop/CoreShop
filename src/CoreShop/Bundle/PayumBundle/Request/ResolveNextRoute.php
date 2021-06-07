<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PayumBundle\Request;

use Payum\Core\Request\Generic;

class ResolveNextRoute extends Generic implements ResolveNextRouteInterface
{
    private string $routeName;
    private array $routeParameters = [];

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    public function setRouteParameters(array $parameters): void
    {
        $this->routeParameters = $parameters;
    }
}
