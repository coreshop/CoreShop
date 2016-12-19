<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\Filter;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

/**
 * Class Service
 * @package CoreShop\Model\Product\Filter
 *
 * @todo: make more override-able
 */
class Service
{
    const EMPTY_STRING = '##EMPTY##';

    /**
     * @param Filter  $filterObject
     * @param Listing $list
     * @param array   $params
     *
     * @return array $currentFilter
     */
    public function initFilterService(Filter $filterObject, Listing $list, $params = [])
    {
        $currentFilter = [];

        if (is_array($filterObject->getFilters())) {
            foreach ($filterObject->getFilters() as $filter) {
                $currentFilter = $filter->addCondition($filterObject, $list, $currentFilter, $params, false);
            }
        }

        if (is_array($filterObject->getPreConditions())) {
            foreach ($filterObject->getPreConditions() as $filter) {
                $currentFilter = $filter->addCondition($filterObject, $list, $currentFilter, $params, true);
            }
        }

        return $currentFilter;
    }
}
