<?php

namespace CoreShop\Component\Store\Repository;

use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\ORM\QueryBuilder;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

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
