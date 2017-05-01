<?php

namespace CoreShop\Bundle\PaymentBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;

class PaymentRepository extends EntityRepository implements PaymentRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForOrderId($orderId)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.orderId = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getResult()
        ;
    }
}