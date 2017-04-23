<?php

namespace CoreShop\Bundle\OrderBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;

class CartPriceRuleVoucherRepository extends EntityRepository implements CartPriceRuleVoucherRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByCode($code)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}