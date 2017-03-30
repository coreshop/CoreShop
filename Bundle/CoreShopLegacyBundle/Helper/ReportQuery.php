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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Helper;

use Pimcore\Admin\Helper\QueryParams;
use Pimcore\Db;

/**
 * Class ReportQuery
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Helper
 */
class ReportQuery
{
    /**
     * Extract filters from query params.
     *
     * @param $params
     * @returns string
     */
    public static function extractFilterDefinition($params = [])
    {
        $allowedFields = ['from', 'to'];
        $conditions = [];
        $db = Db::get();

        if (is_array($params)) {
            foreach ($params as $param => $value) {
                if (in_array($param, $allowedFields)) {
                    switch ($param) {
                        case 'from':
                            $conditions[] = 'o_creationDate >= '.$db->quote($value);
                            break;

                        case 'to':
                            $conditions[] = 'o_creationDate <= '.$db->quote($value);
                            break;
                    }
                }
            }
        }

        if (count($conditions) === 0) {
            $conditions[] = '1=1';
        }

        return implode(' AND ', $conditions);
    }

    /**
     * Convert Extjs Params to ORDER BY String
     *
     * @param array $params
     * @return string
     */
    public static function getSqlSort($params = [])
    {
        $sortingSettings = QueryParams::extractSortingSettings($params);

        $order = [];

        if ($sortingSettings['orderKey']) {
            $order[] = $sortingSettings['orderKey'] . " " . $sortingSettings['order'];
        }

        return count($order) > 0 ? "ORDER BY " . implode(", ", $order) : "";
    }
}
