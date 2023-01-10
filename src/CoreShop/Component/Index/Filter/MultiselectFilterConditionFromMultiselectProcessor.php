<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Condition\ConcatCondition;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class MultiselectFilterConditionFromMultiselectProcessor implements FilterConditionProcessorInterface
{
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter): array
    {
        $field = $condition->getConfiguration()['field'];
        $rawValues = $list->getGroupByValues($field, true);
        $values = [];

        foreach ($rawValues as $v) {
            if ($v['value'] === null) {
                continue;
            }

            $explode = explode(',', $v['value']);

            foreach ($explode as $e) {
                if (empty($e)) {
                    continue;
                }

                if (array_key_exists($e, $values)) {
                    $values[$e]['count'] += $v['count'];

                    continue;
                }

                $values[$e] = ['value' => $e, 'count' => $v['count']];
            }
        }

        return [
            'type' => 'multiselect',
            'label' => $condition->getLabel(),
            'currentValues' => array_map(static function (string $value) {
                return trim($value, ',');
            }, $currentFilter[$field] ?: []),
            'values' => array_values($values),
            'fieldName' => $field,
            'quantityUnit' => $condition->getQuantityUnit() ? Unit::getById($condition->getQuantityUnit()) : null,
        ];
    }

    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter, ParameterBag $parameterBag, bool $isPrecondition = false): array
    {
        $field = $condition->getConfiguration()['field'];
        $values = $parameterBag->get($field);

        if (empty($values)) {
            $values = $condition->getConfiguration()['preSelects'];
        }

        $currentFilter[$field] = $values;

        if ($values === static::EMPTY_STRING) {
            $values = null;
        }

        if (!empty($values)) {
            $fieldName = $isPrecondition ? 'PRECONDITION_' . $field : $field;

            $likeConditions = [];

            foreach ($values as $v) {
                $v = ',' . $v . ',';

                $likeConditions[] = new LikeCondition($field, 'both', $v);
            }

            unset($v);

            $list->addCondition(new ConcatCondition($field, 'OR', $likeConditions), $fieldName);
        }

        return $currentFilter;
    }
}
