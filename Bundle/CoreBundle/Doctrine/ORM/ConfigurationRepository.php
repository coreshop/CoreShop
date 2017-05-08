<?php

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ConfigurationBundle\Doctrine\ORM\ConfigurationRepository as BaseConfigurationRepository;
use CoreShop\Component\Core\Repository\ConfigurationRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class ConfigurationRepository extends BaseConfigurationRepository implements ConfigurationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForKeyAndStore($key, StoreInterface $store)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.key = :configKey')
            ->andWhere('o.store = :store')
            ->setParameter('configKey', $key)
            ->setParameter('store', $store)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}