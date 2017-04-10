<?php

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\TaxationBundle\Doctrine\ORM\TaxRuleRepository as BaseTaxRuleRepository;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Repository\TaxRuleRepositoryInterface;

class TaxRuleRepository extends BaseTaxRuleRepository implements TaxRuleRepositoryInterface
{
    public function findForCountryAndState(TaxRuleGroupInterface $taxRuleGroup, CountryInterface $country = null, StateInterface $state = null)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.taxRuleGroup = :taxRuleGroup')
            ->andWhere('(o.country = :country OR o.country IS NULL)')
            ->andWhere('(o.state  = :state OR o.state IS NULL)')
            ->setParameter('taxRuleGroup', $taxRuleGroup->getId())
            ->setParameter('country', $country ? $country->getId() : 0)
            ->setParameter('state',  $state ? $state->getId() : 0)
            ->getQuery()
            ->getResult()
        ;
    }


}