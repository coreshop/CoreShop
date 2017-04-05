<?php

namespace CoreShop\Component\Index\Factory;

use CoreShop\Component\Index\Model\IndexInterface;

interface ListingFactoryInterface
{
    /**
     * @param IndexInterface $index
     * @return mixed
     */
    public function createList(IndexInterface $index);
}