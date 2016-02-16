<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\Filter;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Index;
use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

/**
 * Class Service
 * @package CoreShop\Model\Product\Filter
 *
 * @todo: Make override able
 */
class Service extends AbstractModel {

    const EMPTY_STRING = "##EMPTY##";

    /**
     * @param Filter $filterObject
     * @param Listing $list
     * @param array $params
     *
     * @return array $currentFilter
     */
    public function initFilterService(Filter $filterObject, Listing $list, $params = array()) {
        $currentFilter = array();

        if(is_array($filterObject->getFilters())) {
            foreach ($filterObject->getFilters() as $filter) {
                $currentFilter = $filter->addCondition($filterObject, $list, $params, false);
            }
        }

        if(is_array($filterObject->getPreConditions())) {
            foreach($filterObject->getPreConditions() as $filter) {
                $currentFilter = $filter->addCondition($filterObject, $list, $params, true);
            }
        }

        return $currentFilter;
    }

}
