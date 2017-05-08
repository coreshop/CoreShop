<?php

namespace CoreShop\Component\Store\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\ORM\QueryBuilder;

interface StoreRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();

    /**
     * @param $siteId
     * @return StoreInterface
     */
    public function findOneBySite($siteId);

    /**
     * @return StoreInterface
     */
    public function findStandard();
}
