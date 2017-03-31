<?php

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

final class ResourceLogEntryRepository extends EntityRepository implements ResourceLogEntryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createByObjectIdQueryBuilder($objectId)
    {
        return $this->createQueryBuilder('log')
            ->where('log.objectId = :objectId')
            ->setParameter('objectId', $objectId)
        ;
    }
}
