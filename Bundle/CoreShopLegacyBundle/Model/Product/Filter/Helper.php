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
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop;

/**
 * Class Helper
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter
 */
class Helper
{
    /**
     * Setup Product List.
     *
     * @param ProductListing $list
     * @param $params
     * @param Filter  $filter
     * @param Service $filterService
     *
     * @return array
     */
    public static function setupProductList(ProductListing $list, $params, Filter $filter, Service $filterService = null)
    {
        $orderKey = $filter->getOrderKey();
        $orderDirection = $filter->getOrder();
        $limit = $filter->getResultsPerPage();

        if ($params['orderKey']) {
            $orderKey = $params['orderKey'];
        }

        if ($params['order']) {
            $orderDirection = $params['order'];
        }

        if ($params['perPage']) {
            $limit = $params['perPage'];
        }

        $list->setOrderKey($orderKey);
        $list->setOrder($orderDirection);
        $list->setLimit($limit);

        $list->setShop(Shop::getShop());

        if ($filterService instanceof Service) {
            return $filterService->initFilterService($filter, $list, $params);
        }

        return [];
    }
}
