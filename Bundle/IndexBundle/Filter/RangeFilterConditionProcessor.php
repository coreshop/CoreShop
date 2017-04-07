<?php

namespace CoreShop\Bundle\IndexBundle\Filter;

use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class RangeFilterConditionProcessor implements FilterConditionProcessorInterface
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
