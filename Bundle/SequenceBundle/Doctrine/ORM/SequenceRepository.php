<?php

namespace CoreShop\Bundle\SequenceBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Sequence\Repository\SequenceRepositoryInterface;

class SequenceRepository extends EntityRepository implements SequenceRepositoryInterface
{
    public function findForType($type)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}