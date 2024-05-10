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

use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\HttpFoundation\ParameterBag;

class CategorySelectConditionProcessor implements FilterConditionProcessorInterface
{
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter): array
    {
        $field = 'categoryIds';
        $includeSubCategories = $condition->getConfiguration()['includeSubCategories'];
        if ($includeSubCategories === true) {
            $field = 'parentCategoryIds';
        }

        $parsedValues = [];
        $rawValues = $list->getGroupByValues($field, true);

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
            'type' => 'category_select',
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

        $value = $parameterBag->get($field);

        $currentFilter[$field] = $value;

        if ($value === static::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value)) {
            $preSelectValue = $condition->getConfiguration()['preSelect'];
            if (is_numeric($preSelectValue)) {
                $value = $preSelectValue;
            }
        }

        if (!empty($value)) {
            $value = '%,' . trim((string) $value) . ',%';
            $fieldName = $isPrecondition ? 'PRECONDITION_' . $field : $field;
            $list->addCondition(new LikeCondition($field, 'both', $value), $fieldName);
        }

        return $currentFilter;
    }
}
