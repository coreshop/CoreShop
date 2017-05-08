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
 *
*/

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\Object\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class BooleanFilterConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $rawValues = [];
        $currentValues = $currentFilter[\Pimcore\File::getValidFilename($condition->getLabel())];
        $fields = $condition->getField();

        if (is_array($fields)) {
            foreach ($condition->getField() as $field) {
                $fieldRawValues = $list->getGroupByValues($field, true);

                if (!is_array($fieldRawValues) || !isset($currentValues[$field])) {
                    continue;
                }

                foreach ($fieldRawValues as $fieldRawValue) {
                    $dbVal = (int)$fieldRawValue['value'];

                    if ($dbVal === 1) {
                        $rawValues[] = [
                            'value' => $field,
                            'count' => $fieldRawValue['count'],
                        ];
                        break;
                    }
                }
            }
        }

        return [
            'type' => 'boolean',
            'label' => $condition->getLabel(),
            'currentValues' => $currentValues,
            'values' => $rawValues,
            'fieldName' => $condition->getField(),
            'quantityUnit' => Unit::getById($condition->getQuantityUnit())
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false)
    {
        $definedValues = (array) $condition->getField(); //Todo: Don't think that this works...

        $values = [];
        $sqlFilter = [];

        $preSelects = (array) $condition->getConfiguration()['preSelects'];

        $isInSearchMode = $this->isInFilterMode($definedValues, $parameterBag);

        foreach ($definedValues as $definedValue) {
            if (isset($params[$definedValue])) {
                $val = $params[$definedValue];
            } elseif ($isInSearchMode === false && in_array($definedValue, $preSelects)) {
                $val = 1;
            } else {
                $val = 0;
            }

            $values[$definedValue] = $val;
        }

        $name = \Pimcore\File::getValidFilename($condition->getLabel());

        foreach ($values as $valueName => $boolValue) {
            $currentFilter[$name][$valueName] = $boolValue;

            if ($boolValue == 1) {
                $sqlFilter[$valueName] = 1;
            }
        }

        if (!empty($sqlFilter)) {
            $fieldName = $isPrecondition ? 'PRECONDITION_'.$name : $name;

            $conditions = [];

            $c = 0;

            foreach ($sqlFilter as $valName => $boolVal) {
                $conditions[] = Condition::match($valName, (int) $boolVal);
                ++$c;
            }

            if (count($conditions) > 0) {
                $list->addCondition(Condition::concat($fieldName, $conditions, 'AND'), $fieldName);
            }
        }

        return $currentFilter;
    }

    /**
     * @param $definedValues
     * @param ParameterBag $parameterBag
     *
     * @return bool
     */
    private function isInFilterMode($definedValues, $parameterBag)
    {
        foreach ($definedValues as $d) {
            if ($parameterBag->has($d)) {
                return true;
            }
        }

        return false;
    }
}
