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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Listing as ProductListing;

/**
 * Class Service
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter
 *
 * @todo: make more override-able
 */
class Service
{
    /**
     * Const for Empty Value
     */
    const EMPTY_STRING = '##EMPTY##';

    /**
     * @param Filter  $filterObject
     * @param ProductListing $list
     * @param array   $params
     *
     * @return array $currentFilter
     */
    public function initFilterService(Filter $filterObject, ProductListing $list, $params = [])
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
