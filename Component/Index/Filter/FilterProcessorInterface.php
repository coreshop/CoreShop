<?php

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

interface FilterProcessorInterface
{
    /**
     * @param FilterInterface  $filter
     * @param ListingInterface $list
     * @param ParameterBag     $parameterBag
     *
     * @return mixed
     */
    public function processConditions(FilterInterface $filter, ListingInterface $list, ParameterBag $parameterBag);

    /**
     * @param FilterInterface $filter
     * @param ListingInterface $list
     * @param $currentFilter
     * @return mixed
     */
    public function prepareConditionsForRendering(FilterInterface $filter, ListingInterface $list, $currentFilter);
}
