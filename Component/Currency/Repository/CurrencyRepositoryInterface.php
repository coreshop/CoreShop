<?php

namespace CoreShop\Component\Currency\Repository;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\QueryBuilder;

interface CurrencyRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();

    /**
     * @return CurrencyInterface[]
     */
    public function findActive();

    /**
     * @param $currencyCode
     * @return CurrencyInterface
     */
    public function getByCode($currencyCode);
}
