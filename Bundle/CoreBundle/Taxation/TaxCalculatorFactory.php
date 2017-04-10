<?php

namespace CoreShop\Bundle\CoreBundle\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Repository\TaxRuleRepositoryInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Calculator\TaxRulesTaxCalculator;

class TaxCalculatorFactory implements TaxCalculatorFactoryInterface
{
    /**
     * @var TaxRuleRepositoryInterface
     */
    private $taxRuleRepository;

    /**
     * @param TaxRuleRepositoryInterface $taxRuleRepository
     */
    public function __construct(TaxRuleRepositoryInterface $taxRuleRepository)
    {
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCalculatorForAddress(TaxRuleGroupInterface $taxRuleGroup, AddressInterface $address)
    {
        //TODO: Caching? Maybe using a decorated one?

        $taxRules = $this->taxRuleRepository->findForCountryAndState($taxRuleGroup, $address->getCountry(), $address->getState());
        $taxRates = [];
        $firstRow = true;
        $behavior = false;

        foreach ($taxRules as $taxRule) {
            $taxRate = $taxRule->getTaxRate();

            $taxRates[] = $taxRate;

            if ($firstRow) {
                $behavior = $taxRule->getBehavior();

                $firstRow = false;
            }

            if ($taxRule->getBehavior() === TaxCalculatorInterface::class) {
                break;
            }
        }

        return new TaxRulesTaxCalculator($taxRates, $behavior);
    }


}