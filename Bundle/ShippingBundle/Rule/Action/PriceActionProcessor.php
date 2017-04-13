<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class PriceActionProcessor implements CarrierPriceActionProcessorInterface
{
    /**
     * @var TaxCalculatorInterface
     */
    private $taxCalculator;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(TaxCalculatorFactoryInterface $taxCalculatorFactory)
    {
        $this->taxCalculatorFactory = $taxCalculatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, AddressInterface $address, array $configuration, $withTax = true)
    {
        //TODO: Check for Base-Configurations if prices are gross/net
        $price = $configuration['price'];

        if ($withTax) {
            $taxCalculator = $this->getTaxCalculator($carrier, $address);

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $price = $taxCalculator->applyTaxes($price);
            }
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    private function getTaxCalculator(CarrierInterface $carrier, AddressInterface $address)
    {
        if (is_null($this->taxCalculator)) {
            $taxRuleGroup = $carrier->getTaxRule();

            if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
                $this->taxCalculator = $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
            }
            else {
                $this->taxCalculator = null;
            }
        }

        return $this->taxCalculator;
    }

    public function getModification(CarrierInterface $carrier, AddressInterface $address, $price, array $configuration)
    {
        return 0;
    }
}