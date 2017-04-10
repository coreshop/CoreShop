<?php

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Bundle\AddressBundle\Doctrine\ORM\CountryRepository as BaseCountryRepository;
use CoreShop\Component\Store\Model\StoreInterface;

class CountryRepository extends BaseCountryRepository implements CountryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForStore(StoreInterface $store)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.stores', 's')
            ->andWhere('o.active = true')
            ->andWhere('o.id = :storeId')
            ->setParameter('storeId', $store->getId())
            ->getQuery()
            ->getResult()
        ;
    }
}