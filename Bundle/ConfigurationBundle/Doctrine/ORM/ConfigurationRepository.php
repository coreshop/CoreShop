<?php

namespace CoreShop\Bundle\ConfigurationBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Configuration\Repository\ConfigurationRepositoryInterface;

class ConfigurationRepository extends EntityRepository implements ConfigurationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByKey($key)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}