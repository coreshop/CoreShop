<?php

namespace CoreShop\Bundle\NotificationBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;

class NotificationRuleRepository extends EntityRepository implements NotificationRuleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForType($type)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.type = :type')
            ->andWhere('o.active = :active')
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult()
        ;
    }
}