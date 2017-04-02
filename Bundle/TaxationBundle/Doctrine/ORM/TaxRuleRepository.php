<?php

namespace CoreShop\Bundle\TaxationBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Taxation\Repository\TaxRuleRepositoryInterface;

class TaxRuleRepository extends EntityRepository implements TaxRuleRepositoryInterface
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
    public function getByGroupId($taxRuleGroupId)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.taxRuleGroup = :taxRuleGroupId')
            ->setParameter('taxRuleGroupId', $taxRuleGroupId)
            ->getQuery()
            ->getResult()
        ;
    }
}
