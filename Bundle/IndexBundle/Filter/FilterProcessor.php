<?php

namespace CoreShop\Bundle\IndexBundle\Filter;

use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Filter\FilterProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class FilterProcessor implements FilterProcessorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $conditionProcessors;

    /**
     * @param ServiceRegistryInterface $conditionProcessors
     */
    public function __construct(ServiceRegistryInterface $conditionProcessors)
    {
        $this->conditionProcessors = $conditionProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function processConditions(FilterInterface $filter, ListingInterface $list, ParameterBag $parameterBag)
    {
        $currentFilter = [];
        $conditions = $filter->getConditions();
        $preConditions = $filter->getPreConditions();

        if ($filter->hasConditions()) {
            foreach ($conditions as $condition) {
                $currentFilter = $this->getConditionProcessorForCondition($condition)->addCondition($condition, $filter, $list, $currentFilter, $parameterBag, false);
            }
        }

        if ($filter->hasPreConditions()) {
            foreach ($preConditions as $condition) {
                $currentFilter = $this->getConditionProcessorForCondition($condition)->addCondition($condition, $filter, $list, $currentFilter, $parameterBag, true);
            }
        }

        return $currentFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareConditionsForRendering(FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $conditions = $filter->getConditions();
        $preparedConditions = [];

        if ($filter->hasConditions()) {
            foreach ($conditions as $condition) {
                $preparedConditions[$condition->getId()] = $this->getConditionProcessorForCondition($condition)->prepareValuesForRendering($condition, $filter, $list, $currentFilter);
            }
        }

        return $preparedConditions;
    }

    /**
     * @param FilterConditionInterface $condition
     *
     * @return FilterConditionProcessorInterface
     */
    private function getConditionProcessorForCondition(FilterConditionInterface $condition)
    {
        return $this->conditionProcessors->get($condition->getType());
    }
}
