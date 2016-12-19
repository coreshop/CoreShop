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

namespace CoreShop\Model;

use CoreShop\Exception;
use Pimcore\Tool;

/**
 * Class Service
 * @package CoreShop\Model
 */
class Service
{
    /**
     * @param string $filterJson
     * @param string $class
     *
     * @throws Exception
     *
     * @return string
     */
    public static function getFilterCondition($filterJson, $class)
    {
        if (!Tool::classExists($class)) {
            throw new Exception("Class '$class' not found");
        }

        $classFields = get_class_vars($class);

        // create filter condition
        $conditionPartsFilters = [];

        if ($filterJson) {
            $db = \Pimcore\Db::get();
            $filters = \Zend_Json::decode($filterJson);
            foreach ($filters as $filter) {
                $operator = '=';

                $filterField = $filter['property'];
                $filterOperator = $filter['operator'];

                if ($filter['type'] == 'string') {
                    $operator = 'LIKE';
                } elseif ($filter['type'] == 'numeric') {
                    if ($filterOperator == 'lt') {
                        $operator = '<';
                    } elseif ($filterOperator == 'gt') {
                        $operator = '>';
                    } elseif ($filterOperator == 'eq') {
                        $operator = '=';
                    }
                } elseif ($filter['type'] == 'date') {
                    if ($filterOperator == 'lt') {
                        $operator = '<';
                    } elseif ($filterOperator == 'gt') {
                        $operator = '>';
                    } elseif ($filterOperator == 'eq') {
                        $operator = '=';
                    }
                    $filter['value'] = strtotime($filter['value']);
                } elseif ($filter['type'] == 'list') {
                    $operator = 'in';
                } elseif ($filter['type'] == 'boolean') {
                    $operator = '=';
                    $filter['value'] = (int) $filter['value'];
                }

                if (array_key_exists($filterField, $classFields)) {
                    if ($operator === 'in') {
                        $conditionPartsFilters[] = '`'.$filterField.'` '.$operator.' ('.$db->quote($filter['value']).')';
                    } else {
                        $conditionPartsFilters[] = '`'.$filterField.'` '.$operator.' '.$db->quote($filter['value']);
                    }
                }
            }
        }

        $conditionFilters = '1 = 1';
        if (count($conditionPartsFilters) > 0) {
            $conditionFilters = '('.implode(' AND ', $conditionPartsFilters).')';
        }

        return $conditionFilters;
    }
}
