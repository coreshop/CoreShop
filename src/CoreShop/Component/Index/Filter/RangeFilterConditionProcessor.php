<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Condition\RangeCondition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class RangeFilterConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $field = $condition->getConfiguration()['field'];
        $rawValues = $list->getGroupByValues($field, true);

        $minValue = count($rawValues) > 0 ? $rawValues[0]['value'] : 0;
        $maxValue = count($rawValues) > 0 ? $rawValues[count($rawValues) - 1]['value'] : 0;

        return [
            'type' => 'range',
            'label' => $condition->getLabel(),
            'minValue' => $minValue,
            'maxValue' => $maxValue,
            'currentValueMin' => $currentFilter[$field . '-min'] ? $currentFilter[$field . '-min'] : $minValue,
            'currentValueMax' => $currentFilter[$field . '-max'] ? $currentFilter[$field . '-max'] : $maxValue,
            'values' => array_values($rawValues),
            'fieldName' => $field,
            'stepCount' => $condition->getConfiguration()['stepCount'],
            'quantityUnit' => Unit::getById($condition->getQuantityUnit()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false)
    {
        $field = $condition->getConfiguration()['field'];

        if ($parameterBag->has($field)) {
            $values = explode(',', $parameterBag->get($field));

            $parameterBag->set($field . '-min', $values[0]);
            $parameterBag->set($field . '-max', $values[0]);
        }

        $valueMin = $parameterBag->get($field . '-min');
        $valueMax = $parameterBag->get($field . '-max');

        if (empty($valueMax)) {
            $valueMax = $condition->getConfiguration()['preSelectMax'];
        }

        if ($valueMax === static::EMPTY_STRING) {
            $valueMax = null;
        }

        if (empty($valueMin)) {
            $valueMin = $condition->getConfiguration()['preSelectMin'];
        }

        if ($valueMin === static::EMPTY_STRING) {
            $valueMin = null;
        }

        $currentFilter[$field . '-min'] = $valueMin;
        $currentFilter[$field . '-max'] = $valueMax;

        if (!empty($valueMin) && !empty($valueMax)) {
            $fieldName = $field;

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_' . $fieldName;
            }

            $list->addCondition(new RangeCondition($field, $valueMin, $valueMax), $fieldName);
        }

        return $currentFilter;
    }
}
