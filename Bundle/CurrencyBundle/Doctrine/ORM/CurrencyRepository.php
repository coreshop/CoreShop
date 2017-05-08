<?php

namespace CoreShop\Bundle\CurrencyBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;

class CurrencyRepository extends EntityRepository implements CurrencyRepositoryInterface
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
    public function findActive()
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.countries', 'c')
            ->andWhere('c.active = true')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($currencyCode)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isoCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
