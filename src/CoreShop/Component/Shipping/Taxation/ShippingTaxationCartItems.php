<?php
declare(strict_types=1);

namespace CoreShop\Component\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributor;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

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
        CarrierInterface $carrier,
        AddressInterface $address,
        array $usedTaxes
    ) {
        $totalAmount = [];
        $taxRuleGroup = [];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if ($item->getDigitalProduct() === true) {
                continue;
            }

            if (!$item->getProduct()->getTaxRule()) {
                continue;
            }

            $taxRule = $item->getProduct()->getTaxRule();

            if (!\in_array($taxRule->getId(), $totalAmount, true)) {
                $totalAmount[$taxRule->getId()] = 0;
            }

            $totalAmount[$taxRule->getId()] += $item->getTotal(true);
            $taxRuleGroup[] = $taxRule;
        }

        if (\count($taxRuleGroup) === 0) {
            return $usedTaxes;
        }

        $shippingAdjustments = $cart->getAdjustments(AdjustmentInterface::SHIPPING);
        if (!$shippingAdjustments) {
            return $usedTaxes;
        }

        /** @var Fieldcollection\Data\CoreShopAdjustment $shippingAdjustment */
        $shippingAdjustment = $shippingAdjustments[0];
        $shippingPrice = $shippingAdjustment->getPimcoreAmountGross();
        $distributor = new ProportionalIntegerDistributor();
        $distributedAmount = $distributor->distribute($totalAmount, $shippingPrice);

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

        $cart->removeAdjustments(AdjustmentInterface::SHIPPING);
        $shippingAdjustment->setPimcoreAmountNet($shippingAdjustment->getPimcoreAmountGross() - $shippingTaxAmount);
        $cart->addAdjustment($shippingAdjustment);

        return $usedTaxes;
    }
}
