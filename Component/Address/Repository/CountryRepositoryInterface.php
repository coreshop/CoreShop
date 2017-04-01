<?php

namespace CoreShop\Component\Address\Repository;

use Doctrine\ORM\QueryBuilder;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Core\Repository\RepositoryInterface;

interface CountryRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();

    /**
     * @param string $name
     * @param string $locale
     *
     * @return CountryInterface[]
     */
    public function findByName($name, $locale);
}
