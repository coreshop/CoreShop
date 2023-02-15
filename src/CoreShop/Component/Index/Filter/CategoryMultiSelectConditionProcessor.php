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
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategoryMultiSelectConditionProcessor implements FilterConditionProcessorInterface
{
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter): array
    {
        $field = 'categoryIds';
        $includeSubCategories = $condition->getConfiguration()['includeSubCategories'];
        if ($includeSubCategories === true) {
            $field = 'parentCategoryIds';
        }

        $concatenator = $condition->getConfiguration()['concatenator'] ?: 'OR';
        $parsedValues = [];
        $rawValues = $list->getGroupByValues($field, true, $concatenator == 'AND' ? false : true);

        foreach ($rawValues as $v) {
            if ($v['value'] === null) {
                continue;
            }

            $explode = explode(',', $v['value']);
            foreach ($explode as $e) {
                if (empty($e)) {
                    continue;
                }

                if (isset($parsedValues[$e])) {
                    $count = $parsedValues[$e]['count'] + (int) $v['count'];
                } else {
                    $count = (int) $v['count'];
                }

                $parsedValues[$e] = ['value' => $e, 'count' => $count];
            }
        }

        $values = array_values($parsedValues);

        $objects = [];
        foreach ($values as $value) {
            $object = Concrete::getById((int) $value['value']);
            if ($object instanceof Concrete) {
                $objects[] = $object;
            }
        }

        return [
            'type' => 'category_multiselect',
            'label' => $condition->getLabel(),
            'currentValues' => $currentFilter[$field],
            'includeSubCategories' => $includeSubCategories,
            'values' => $values,
            'objects' => $objects,
            'fieldName' => $field,
        ];
    }

    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter, ParameterBag $parameterBag, bool $isPrecondition = false): array
    {
        $field = 'categoryIds';
        $includeSubCategories = $condition->getConfiguration()['includeSubCategories'];
        if ($includeSubCategories === true) {
            $field = 'parentCategoryIds';
        }

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

            $concatenator = $condition->getConfiguration()['concatenator'] ?: 'OR';

            $list->addCondition(new ConcatCondition($field, $concatenator, $likeConditions), $fieldName);
        }

        return $currentFilter;
    }
}
