<?php

namespace CoreShop\Component\Resource\Repository;

use Pimcore\Model\Listing\AbstractListing;
use Pimcore\Model\Object\Concrete;

interface PimcoreRepositoryInterface
{
    /**
     * @return AbstractListing
     */
    public function getList();

    /**
     * @param $id
     *
     * @return Concrete
     */
    public function find($id);
}
