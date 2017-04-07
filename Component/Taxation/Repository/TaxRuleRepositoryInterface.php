<?php

namespace CoreShop\Component\Taxation\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface TaxRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $groupId
     *
     * @return mixed
     */
    public function getByGroupId($groupId);
}
