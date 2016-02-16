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

class Helper {

    /**
     * @param Filter $filter
     * @param Listing $list
     * @param $params
     * @param Service $filterService
     *
     * @return array()
     */
    public static function setupProductList(Listing $list, $params, Filter $filter = null, Service $filterService = null) {
        $orderKey = $filter->getOrderKey();
        $orderDirection = $filter->getOrder();

        if($params['orderKey']) {
            $orderKey = $params['orderKey'];
        }

        if($params['order']) {
            $orderDirection = $params['order'];
        }

        $limit = $filter->getResultsPerPage();

        if($params['perPage']) {
            $limit = $params['perPage'];
        }

        $list->setOrderKey($orderKey);
        $list->setOrder($orderDirection);
        $list->setLimit($limit);

        if($filterService instanceof Service) {
            return $filterService->initFilterService($filter, $list, $params);
        }

        return array();
    }

}