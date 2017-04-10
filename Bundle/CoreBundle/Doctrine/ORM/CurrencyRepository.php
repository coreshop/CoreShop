<?php

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\CurrencyBundle\Doctrine\ORM\CurrencyRepository as BaseCurrencyRepository;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class CurrencyRepository extends BaseCurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActiveForStore(StoreInterface $store)
    {
         return $this->createQueryBuilder('o')
             ->leftJoin('o.countries', 'c')
             ->innerJoin('c.stores', 's')
             ->andWhere('c.active = true')
             ->andWhere('s.id = :storeId')
             ->setParameter('storeId', $store->getId())
             ->distinct()
             ->getQuery()
             ->getResult()
        ;
    }
}