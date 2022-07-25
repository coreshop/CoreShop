<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippableItemInterface;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Webmozart\Assert\Assert;

class TaxCalculationStrategyCartItems implements TaxCalculationStrategyInterface
{
    public function __construct(
        private TaxCollectorInterface $taxCollector,
        private TaxCalculatorFactoryInterface $taxCalculationFactory,
        private ProportionalIntegerDistributorInterface $distributor
    ) {
    }

    public function calculateShippingTax(
        ShippableInterface $shippable,
        CarrierInterface $carrier,
        AddressInterface $address,
        int $shippingAmount,
        array $context = [],
    ): array {
        /**
         * @var StoreAwareInterface $shippable
         * @var ShippableInterface $shippable
         */
        Assert::isInstanceOf($shippable, StoreAwareInterface::class);

        $store = $shippable->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        [$totalAmount, $taxRules] = $this->collectCartItemsTaxRules($shippable);

        if (!$totalAmount || !$taxRules) {
            return [];
        }

        $distributedAmount = $this->distributor->distribute(\array_values($totalAmount), $shippingAmount);

        return $this->collectTaxes($address, $taxRules, $distributedAmount, $store->getUseGrossPrice(), $context);
    }

    private function collectCartItemsTaxRules(ShippableInterface $cart): array
    {
        $totalAmount = [];
        $taxRules = [];

        /**
         * @var ShippableItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if ($item instanceof \CoreShop\Component\Core\Model\OrderItemInterface &&
                $item->getDigitalProduct() === true
            ) {
                continue;
            }

            if (!$item->getProduct() instanceof PurchasableInterface) {
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

        return [$totalAmount, $taxRules];
    }

    private function collectTaxes(
        AddressInterface $address,
        array $taxRuleGroup,
        array $distributedAmount,
        bool $useGrossValues,
        array $context = [],
    ): array {
        $taxes = [];

        foreach ($distributedAmount as $i => $amount) {
            $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress(
                $taxRuleGroup[$i],
                $address,
                $context
            );

            if ($useGrossValues) {
                $shippingTax = $this->taxCollector->collectTaxesFromGross($taxCalculator, $amount);
            } else {
                $shippingTax = $this->taxCollector->collectTaxes($taxCalculator, $amount);
            }
            /** @psalm-suppress InvalidArgument */
            $taxes = $this->taxCollector->mergeTaxes($shippingTax, $taxes);
        }

        return $taxes;
    }
}
