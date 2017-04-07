<?php

namespace CoreShop\Bundle\IndexBundle\Filter;

use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class BooleanFilterConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, ParameterBag $parameterBag)
    {
        return '';
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
