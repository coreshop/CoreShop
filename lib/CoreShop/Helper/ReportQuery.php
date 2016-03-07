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

namespace CoreShop\Helper;

use Pimcore\Db;

class ReportQuery
{
    /**
     * @param $params
     * @returns string
     */
    public static function extractFilterDefinition($params = array())
    {
        $allowedFields = array("from", "to");
        $conditions = [];
        $db = Db::get();

        if(is_array($params)) {
            foreach ($params as $param => $value) {
                if (in_array($param, $allowedFields)) {
                    switch ($param) {
                        case "from":
                            $conditions[] = "o_creationDate >= " . $db->quote($value);
                            break;

                        case "to":
                            $conditions[] = "o_creationDate <= " . $db->quote($value);
                            break;
                    }
                }
            }
        }

        if(count($conditions) === 0) {
            $conditions[] = "1=1";
        }

        return implode(" AND ", $conditions);
    }
}
