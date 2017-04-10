<?php

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface TaxCalculatorFactoryInterface {
    /**
     * @param AddressInterface $address
     * @param TaxRuleGroupInterface $taxRuleGroup
     * @return TaxCalculatorInterface
     */
    public function getTaxCalculatorForAddress(TaxRuleGroupInterface $taxRuleGroup, AddressInterface $address);
}