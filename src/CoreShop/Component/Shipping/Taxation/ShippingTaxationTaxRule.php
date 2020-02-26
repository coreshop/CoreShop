<?php
declare(strict_types=1);

namespace CoreShop\Component\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroup;

class ShippingTaxationTaxRule implements ShippingTaxationInterface
{
    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculationFactory;

    /**
     * @param TaxCollectorInterface $taxCollector
     * @param TaxCalculatorFactoryInterface $taxCalculationFactory
     */
    public function __construct(
        TaxCollectorInterface $taxCollector,
        TaxCalculatorFactoryInterface $taxCalculationFactory
    ) {
        $this->taxCollector = $taxCollector;
        $this->taxCalculationFactory = $taxCalculationFactory;
    }

    /**
     * @inheritDoc
     */
    public function calculateShippingTax(
        CartInterface $cart,
        Carrier $carrier,
        AddressInterface $address,
        array $usedTaxes
    ) {
        $taxRule = $carrier->getTaxRule();

        if (!$taxRule instanceof TaxRuleGroup) {
            return $usedTaxes;
        }

        $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($taxRule, $address);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            $cart->setShippingTaxRate($taxCalculator->getTotalRate());
            $shipping = $cart->getShipping(false);

            return $this->taxCollector->mergeTaxes($this->taxCollector->collectTaxes($taxCalculator, $shipping), $usedTaxes);
        }

        return $usedTaxes;
    }
}
