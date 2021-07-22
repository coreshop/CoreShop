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

namespace CoreShop\Component\Pimcore\BCLayer;

use Pimcore\Model\DataObject\Service;

if (class_exists(\Pimcore\Bundle\AdminBundle\Helper\GridHelperService::class)) {
    class GridHelperService extends \Pimcore\Bundle\AdminBundle\Helper\GridHelperService
    {
    }
} else {
    class GridHelperService
    {
        public function getFilterCondition($filterJson, $class)
        {
            $serviceClass = Service::class;

            if (method_exists($serviceClass, 'getFilterCondition')) {
                return $serviceClass::getFilterCondition($filterJson, $class);
            }

            throw new \InvalidArgumentException(
                sprintf(
                    'Expected class %s to exist or method %s:getFilterCondition to exist',
                    self::class,
                    Service::class
                )
            );
        }
    }
}
