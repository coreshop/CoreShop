<?php

namespace CoreShop\Bundle\StoreBundle\Repository;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;

class StoreRepository extends EntityRepository implements StoreRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySite($siteId)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findStandard()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isDefault = true')
            ->getQuery()
            ->getOneOrNullResult();
    }
}