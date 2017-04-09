<?php

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

interface FilterConditionProcessorInterface
{
    /**
     * Const for Empty Value.
     */
    const EMPTY_STRING = '##EMPTY##';

    /**
     * @param FilterConditionInterface $condition
     * @param FilterInterface          $filter
     * @param ListingInterface         $list
     * @param $currentFilter
     * @param ParameterBag $parameterBag
     * @param bool         $isPrecondition
     *
     * @return mixed
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false);
}
