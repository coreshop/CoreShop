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

namespace CoreShop\Bundle\PayumBundle\Factory;

use CoreShop\Bundle\PayumBundle\Request\ResolveNextRoute;
use CoreShop\Bundle\PayumBundle\Request\ResolveNextRouteInterface;

final class ResolveNextRouteFactory implements ResolveNextRouteFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createNewWithModel($model): ResolveNextRouteInterface
    {
        return new ResolveNextRoute($model);
    }
}