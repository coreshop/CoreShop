<?php

namespace CoreShop\Bundle\ProductBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Product\Repository\ProductPriceRuleRepositoryInterface;

class ProductPriceRuleRepository extends EntityRepository implements ProductPriceRuleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActive()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.active = 1')
            ->getQuery()
            ->getResult()
        ;
    }
}