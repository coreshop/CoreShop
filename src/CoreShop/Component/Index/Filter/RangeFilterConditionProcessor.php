<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\Object\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class RangeFilterConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $rawValues = $list->getGroupByValues($condition->getField(), true);

        $minValue = count($rawValues) > 0 ? $rawValues[0]['value'] : 0;
        $maxValue = count($rawValues) > 0 ? $rawValues[count($rawValues) - 1]['value'] : 0;

        return [
            'type' => 'range',
            'label' => $condition->getLabel(),
            'minValue' => $minValue,
            'maxValue' => $maxValue,
            'currentValueMin' => $currentFilter[$condition->getField().'-min'] ? $currentFilter[$condition->getField().'-min'] : $minValue,
            'currentValueMax' => $currentFilter[$condition->getField().'-max'] ? $currentFilter[$condition->getField().'-max'] : $maxValue,
            'values' => array_values($rawValues),
            'fieldName' => $condition->getField(),
            'stepCount' => $condition->getConfiguration()['stepCount'],
            'quantityUnit' => Unit::getById($condition->getQuantityUnit()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false)
    {
        if ($parameterBag->has($condition->getField())) {
            $values = explode(',', $parameterBag->get($condition->getField()));

            $parameterBag->set($condition->getField().'-min', $values[0]);
            $parameterBag->set($condition->getField().'-max', $values[0]);
        }

        $valueMin = $parameterBag->get($condition->getField().'-min');
        $valueMax = $parameterBag->get($condition->getField().'-max');

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

        $currentFilter[$condition->getField().'-min'] = $valueMin;
        $currentFilter[$condition->getField().'-max'] = $valueMax;

        if (!empty($valueMin) && !empty($valueMax)) {
            $fieldName = $condition->getField();

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_'.$fieldName;
            }

            $list->addCondition(Condition::range($condition->getField(), $valueMin, $valueMax), $fieldName);
        }

        return $currentFilter;
    }
}
