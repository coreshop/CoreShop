<?php

namespace CoreShop\Bundle\IndexBundle\Filter;

use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\Object\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class SelectFilterConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $rawValues = $list->getGroupByValues($condition->getField(), true);

        return [
            'type' => 'select',
            'label' => $condition->getLabel(),
            'currentValue' => $currentFilter[$condition->getField()],
            'values' => array_values($rawValues),
            'fieldName' => $condition->getField(),
            'quantityUnit' => Unit::getById($condition->getQuantityUnit())
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false)
    {
        $value = $parameterBag->get($condition->getField());

        if (empty($value)) {
            $value = $condition->getConfiguration()['preSelect'];
        }

        $value = trim($value);

        $currentFilter[$condition->getField()] = $value;

        if (!empty($value)) {
            $fieldName = $condition->getField();

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_'.$fieldName;
            }

            $list->addCondition(Condition::match($condition->getField(), $value), $fieldName);
        }

        return $currentFilter;
    }
}
