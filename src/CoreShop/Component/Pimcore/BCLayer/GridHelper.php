<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\BCLayer;

use Pimcore\Bundle\AdminBundle\Helper\GridHelperService;
use Pimcore\Model\DataObject\Service;

class GridHelper
{
    public static function getFilterCondition($filterJson, $class)
    {
        if (class_exists(GridHelperService::class)) {
            $gridHelper = new GridHelperService();
            return $gridHelper->getFilterCondition($filterJson, $class);
        }

        $serviceClass = Service::class;

        if (method_exists($serviceClass, 'getFilterCondition')) {
            return $serviceClass::getFilterCondition($filterJson, $class);
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Expected class %s to exist or method %s:getFilterCondition to exist',
                GridHelperService::class,
                Service::class
            )
        );
    }
}
