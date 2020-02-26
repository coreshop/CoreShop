<?php
declare(strict_types=1);

namespace CoreShop\Component\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributorInterface;
use CoreShop\Component\Order\Model\Adjustment;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;

class ShippingTaxationCartItems implements ShippingTaxationInterface
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
     * @var ProportionalIntegerDistributorInterface
     */
    private $distributor;

    public function __construct(
        TaxCollectorInterface $taxCollector,
        TaxCalculatorFactoryInterface $taxCalculationFactory,
        ProportionalIntegerDistributorInterface $distributor
    ) {
        $this->taxCollector = $taxCollector;
        $this->taxCalculationFactory = $taxCalculationFactory;
        $this->distributor = $distributor;
    }

    /**
     * @inheritDoc
     */
    public function calculateShippingTax(
        CartInterface $cart,
        Carrier $carrier,
        AddressInterface $address,
        array $usedTaxes
    ): array {
        $totalAmount = [];
        $taxRules = [];
        $this->collectCartItemsTaxRules($cart, $totalAmount, $taxRules);

        if (!$totalAmount || !$taxRules) {
            return $usedTaxes;
        }

        $shippingAdjustments = $cart->getAdjustments(AdjustmentInterface::SHIPPING);
        if (!$shippingAdjustments) {
            return $usedTaxes;
        }

        $shippingAdjustment = \array_pop($shippingAdjustments);
        $shippingPrice = $shippingAdjustment->getAmount(true);

        $distributedAmount = $this->distributor->distribute(\array_values($totalAmount), $shippingPrice);

        $shippingTaxAmount = $this->collectTaxes($address, $taxRules, $distributedAmount, $usedTaxes);

        $this->updateShippingAdjustment($cart, $shippingAdjustment, $shippingTaxAmount);

        return $usedTaxes;
    }

    private function collectCartItemsTaxRules(CartInterface $cart, array &$totalAmount, array &$taxRules): void
    {
        /** @var CartItemInterface $item */
        foreach ($cart->getItems() as $item) {
            if ($item->getDigitalProduct() === true) {
                continue;
            }

            if (!$item->getProduct()->getTaxRule()) {
                continue;
            }

            $taxRule = $item->getProduct()->getTaxRule();

            if (!\array_key_exists($taxRule->getId(), $totalAmount)) {
                $totalAmount[$taxRule->getId()] = 0;
            }

            $totalAmount[$taxRule->getId()] += $item->getTotal(true);
            $taxRules[] = $taxRule;
        }
    }

    private function collectTaxes(
        AddressInterface $address,
        array $taxRuleGroup,
        array $distributedAmount,
        array &$usedTaxes
    ): int {
        $shippingTaxAmount = 0;
        foreach ($distributedAmount as $i => $amount) {
            $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($taxRuleGroup[$i], $address);

            if (!$taxCalculator) {
                continue;
            }

            $shippingTax = $this->taxCollector->collectTaxesFromGross($taxCalculator, $amount);
            $usedTaxes = $this->taxCollector->mergeTaxes($shippingTax, $usedTaxes);
            $shippingTaxAmount += \array_shift($shippingTax)->getAmount();
        }

        return $shippingTaxAmount;
    }

    private function updateShippingAdjustment(
        CartInterface $cart,
        Adjustment $shippingAdjustment,
        int $shippingTaxAmount
    ): void {
        $cart->removeAdjustments(AdjustmentInterface::SHIPPING);
        $shippingAdjustment->setPimcoreAmountNet($shippingAdjustment->getPimcoreAmountGross() - $shippingTaxAmount);
        $cart->addAdjustment($shippingAdjustment);
    }
}
