<?php

namespace CoreShop\Bundle\ShippingBundle\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

final class CarrierPriceCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $shippingCalculatorRegistry;

    /**
     * @var TaxCalculatorInterface
     */
    private $taxCalculator;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @param PrioritizedServiceRegistryInterface $shippingCalculatorRegistry
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(
        PrioritizedServiceRegistryInterface $shippingCalculatorRegistry,
        TaxCalculatorFactoryInterface $taxCalculatorFactory
    )
    {
        $this->shippingCalculatorRegistry = $shippingCalculatorRegistry;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true)
    {
        $netPrice = 0;

        /**
         * @var $calculator CarrierPriceCalculatorInterface
         */
        foreach ($this->shippingCalculatorRegistry->all() as $calculator) {
            $price = $calculator->getPrice($carrier, $cart, $address, $withTax);

            if (false !== $price && null !== $price) {
                $netPrice = $price;
                break;
            }
        }

        if ($withTax) {
            $taxCalculator = $this->getTaxCalculator($carrier, $address);

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $netPrice = $taxCalculator->applyTaxes($netPrice);
            }
        }

        return $netPrice;
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
}
