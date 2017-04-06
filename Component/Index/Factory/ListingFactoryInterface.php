<?php

namespace CoreShop\Component\Index\Factory;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;

interface ListingFactoryInterface
{
    /**
     * @param IndexInterface $index
     * @return ListingInterface
     */
    public function createList(IndexInterface $index);
}