<?php

namespace CoreShop\Component\Index\Factory;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

interface FilteredListingFactoryInterface
{
    /**
     * @param FilterInterface $filter
     * @param ParameterBag $parameterBag
     * @return ListingInterface
     */
    public function createList(FilterInterface $filter, ParameterBag $parameterBag);
}