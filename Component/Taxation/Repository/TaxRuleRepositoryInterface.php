<?php

namespace CoreShop\Component\Taxation\Repository;

use Doctrine\ORM\QueryBuilder;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface TaxRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $groupId
     * @return mixed
     */
    public function getByGroupId($groupId);
}
