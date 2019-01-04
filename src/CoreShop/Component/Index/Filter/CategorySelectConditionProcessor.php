<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $field = 'categoryIds';
        $includeSubCategories = $condition->getConfiguration()['includeSubCategories'];
        if ($includeSubCategories === true) {
            $field = 'parentCategoryIds';
        }

        $parsedValues = [];
        $rawValues = $list->getGroupByValues($field, true);

        foreach ($rawValues as $v) {
            $explode = explode(',', $v['value']);
            foreach ($explode as $e) {
                if (empty($e)) {
                    continue;
                }
                if ($parsedValues[$e]) {
                    $count = (int) $parsedValues[$e]['count'] + (int) $v['count'];
                } else {
                    $count = (int) $v['count'];
                }
                $parsedValues[$e] = ['value' => $e, 'count' => (int) $count];
            }
        }

        $values = array_values($parsedValues);

        $objects = [];
        foreach ($values as $value) {
            $object = Concrete::getById($value['value']);
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

    /**
     * {@inheritdoc}
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false)
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
            $value = '%,' . trim($value) . ',%';
            $fieldName = $isPrecondition ? 'PRECONDITION_' . $field : $field;
            $list->addCondition(new LikeCondition($field, 'both', $value), $fieldName);
        }

        return $currentFilter;
    }
}
