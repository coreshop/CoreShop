<?php

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

interface ResourceLogEntryRepositoryInterface
{
    /**
     * @param string $objectId
     *
     * @return QueryBuilder
     */
    public function createByObjectIdQueryBuilder($objectId);
}
