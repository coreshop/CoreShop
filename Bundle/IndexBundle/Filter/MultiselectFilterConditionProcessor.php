<?php

namespace CoreShop\Bundle\IndexBundle\Filter;

use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class MultiselectFilterConditionProcessor implements FilterConditionProcessorInterface
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
        $values = $parameterBag->get($condition->getField());

        if (empty($values)) {
            $values = $condition->getConfiguration()['preSelects'];
        }

        $currentFilter[$condition->getField()] = $values;

        if ($values === static::EMPTY_STRING) {
            $values = null;
        }

        if (!empty($values)) {
            $fieldName = $isPrecondition ? 'PRECONDITION_'.$condition->getField() : $condition->getField();

            if (!empty($values)) {
                $list->addCondition(Condition::in($fieldName, $values), $fieldName);
            }
        }

        return $currentFilter;
    }
}
