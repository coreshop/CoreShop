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

namespace CoreShop\Model\Product\Filter\Condition;

use CoreShop\IndexService\Condition;
use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

/**
 * Class Boolean
 * @package CoreShop\Model\Product\Filter\Condition
 */
class Boolean extends AbstractCondition
{
    /**
     * @var string
     */
    public $type = 'boolean';

    /**
     * @var mixed
     */
    public $preSelects;

    /**
     * @return mixed
     */
    public function getPreSelects()
    {
        return $this->preSelects;
    }

    /**
     * @param mixed $preSelects
     */
    public function setPreSelects($preSelects)
    {
        $this->preSelects = $preSelects;
    }

    /**
     * render HTML for filter.
     *
     * @param Filter  $filter
     * @param Listing $list
     * @param $currentFilter
     *
     * @return mixed
     */
    public function render(Filter $filter, Listing $list, $currentFilter)
    {
        $rawValues = [];
        $currentValues = $currentFilter[\Pimcore\File::getValidFilename($this->getLabel())];

        foreach ($this->getField() as $field) {
            $fieldRawValues = $list->getGroupByValues($field, true);

            if (!is_array($fieldRawValues) || !isset($currentValues[ $field ])) {
                continue;
            }

            foreach ($fieldRawValues as $fieldRawValue) {
                $dbVal = (int) $fieldRawValue['value'];

                if ($dbVal === 1) {
                    $rawValues[] = [
                        'value' => $field,
                        'count' => $fieldRawValue['count'],
                    ];
                    break;
                }
            }
        }

        $script = $this->getViewScript($filter, $list, $currentFilter);

        return $this->getView()->partial($script, [
            'label' => $this->getLabel(),
            'currentValues' => $currentValues,
            'values' => $rawValues,
            'fieldname' => $this->getField(),
            'quantityUnit' => $this->getQuantityUnit()
        ]);
    }

    /**
     * add Condition to Product List.
     *
     * @param Filter  $filter
     * @param Listing $list
     * @param $currentFilter
     * @param $params
     * @param bool $isPrecondition
     *
     * @return array $currentFilter
     */
    public function addCondition(Filter $filter, Listing $list, $currentFilter, $params, $isPrecondition = false)
    {
        $definedValues = (array) $this->getField();

        $values = [];
        $sqlFilter = [];

        $preSelects = (array) $this->getPreSelects();

        $isInSearchMode = $this->isInFilterMode($definedValues, $params);

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

        $name = \Pimcore\File::getValidFilename($this->getLabel());

        foreach ($values as $valueName => $boolValue) {
            $currentFilter[$name][$valueName] = $boolValue;

            if ($boolValue == 1) {
                $sqlFilter[ $valueName ] = 1;
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
                $list->addCondition(Condition::concat($fieldName, $conditions, "AND"), $fieldName);
            }
        }

        return $currentFilter;
    }

    /**
     * @param $definedValues
     * @param $params
     * @return bool
     */
    private function isInFilterMode($definedValues, $params)
    {
        foreach ($definedValues as $d) {
            if (isset($params[$d])) {
                return true;
            }
        }

        return false;
    }
}
