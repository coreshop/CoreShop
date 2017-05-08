<?php

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Taxation\Repository\TaxRuleRepositoryInterface as BaseTaxRuleRepositoryInterface;

interface TaxRuleRepositoryInterface extends BaseTaxRuleRepositoryInterface
{
    /**
     * @param TaxRuleGroupInterface $taxRuleGroup
     * @param CountryInterface $country
     * @param StateInterface $state
     * @return TaxRuleInterface[]
     */
    public function findForCountryAndState(TaxRuleGroupInterface $taxRuleGroup, CountryInterface $country = null, StateInterface $state = null);
}
