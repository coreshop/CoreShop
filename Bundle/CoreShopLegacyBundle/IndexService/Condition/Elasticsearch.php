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

namespace CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Condition;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Condition as IndexCondition;

/**
 * Class Elasticsearch
 * @package CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Condition
 */
class Elasticsearch extends AbstractRenderer
{
    /**
     * @param IndexCondition $condition
     * @return array
     */
    protected function renderIn(IndexCondition $condition)
    {
        return ["terms" => [
            $condition->getFieldName() => $condition->getValues()
        ]];
    }

    /**
     * @param IndexCondition $condition
     * @return array
     */
    protected function renderLike(IndexCondition $condition)
    {
        $values = $condition->getValues();

        $pattern = $values["pattern"];
        $value = $values["value"];

        $patternValue = '';

        switch ($pattern) {
            case "left":
                $patternValue = '*' . $value;
                break;
            case "right":
                $patternValue = $value . '*';
                break;
            case "both":
                $patternValue = '*' . $value . '*';
                break;
        }

        return ["wildcard" => [
            $condition->getFieldName() => $patternValue
        ]];
    }

    /**
     * @param IndexCondition $condition
     * @return array
     */
    protected function renderRange(IndexCondition $condition)
    {
        $values = $condition->getValues();

        return ["range" => [
            $condition->getFieldName() => [
                "gte" => $values['from'],
                "lte" => $values['to']
            ]
        ]];
    }

    /**
     * @param IndexCondition $condition
     * @return array
     */
    protected function renderConcat(IndexCondition $condition)
    {
        $values = $condition->getValues();
        $rendered = [
            "filter" => [
                $values['operator'] => []
            ]
        ];

        foreach ($values['conditions'] as $cond) {
            $rendered["filter"][$values['operator']][] = $this->render($cond);
        }

        return $rendered;
    }

    /**
     * @param IndexCondition $condition
     * @return array
     * @throws Exception
     */
    protected function renderCompare(IndexCondition $condition)
    {
        $values = $condition->getValues();
        $value = $values['value'];
        $operator = $values['operator'];

        if ($operator === "=" || $operator === "!=") {
            if ($operator === "!=") {
                $rendered = ["not" =>
                    [
                        "term" => [
                            $condition->getFieldName() => $condition->getValues()
                        ]
                    ]
                ];
            } else {
                $rendered = ["term" => [
                    $condition->getFieldName() => $condition->getValues()
                ]];
            }
        } else {
            $map = [
                ">" => "gt",
                ">=" => "gte",
                "<" => "lt",
                "<=" => "lte"
            ];

            if (array_key_exists($operator, $map)) {
                $rendered = ["range" => [
                    $condition->getFieldName() => [
                        $map[$operator] => $value
                    ]
                ]];
            } else {
                throw new Exception($operator . " is not supported for compare method");
            }
        }

        return $rendered;
    }
}
