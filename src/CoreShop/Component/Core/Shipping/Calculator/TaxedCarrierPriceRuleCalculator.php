<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface as BaseCarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Webmozart\Assert\Assert;

final class TaxedCarrierPriceRuleCalculator implements \CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface
{
    /**
     * @var CarrierPriceCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @var ServiceRegistryInterface
     */
    private $taxCalculatorStrategyRegistry;

    /**
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     * @param ServiceRegistryInterface        $taxCalculatorStrategyRegistry
     */
    public function __construct(
        CarrierPriceCalculatorInterface $carrierPriceCalculator,
        ServiceRegistryInterface $taxCalculatorStrategyRegistry
    ) {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->taxCalculatorStrategyRegistry = $taxCalculatorStrategyRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(
        BaseCarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        $withTax = true
    ) {
        /**
         * @var CartInterface $shippable
         */
        Assert::isInstanceOf($shippable, CartInterface::class);

        $store = $shippable->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        $useGrossPrice = $store->getUseGrossPrice();

        $price = $this->carrierPriceCalculator->getPrice($carrier, $shippable, $address);

        if ($useGrossPrice && $withTax) {
            return $price;
        }

        if (!$useGrossPrice && !$withTax) {
            return $price;
        }

        $shippingTaxCalculationStrategy = $carrier->getTaxCalculationStrategy() ?? 'taxRule';

        if (!$this->taxCalculatorStrategyRegistry->has($shippingTaxCalculationStrategy)) {
            throw new \InvalidArgumentException(sprintf('Shipping Tax Calculation Strategy %s not found', $shippingTaxCalculationStrategy));
        }

        /**
         * @var TaxCalculationStrategyInterface $taxCalculationService
         */
        $taxCalculationService = $this->taxCalculatorStrategyRegistry->get($shippingTaxCalculationStrategy);
        $cartTaxes = $taxCalculationService->calculateShippingTax($shippable, $carrier, $address, $price);

        $cartTax = array_sum(array_map(static function(TaxItemInterface $taxItem) {
            return $taxItem->getAmount();
        }, $cartTaxes));

        if ($useGrossPrice) {
            return $price - $cartTax;
        }

        return $price + $cartTax;
    }
}
