<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface as BaseCarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Webmozart\Assert\Assert;

final class TaxedCarrierPriceRuleCalculator implements TaxedShippingCalculatorInterface
{
    public function __construct(
        private CarrierPriceCalculatorInterface $carrierPriceCalculator,
        private ServiceRegistryInterface $taxCalculatorStrategyRegistry,
    ) {
    }

    public function getPrice(
        BaseCarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        bool $withTax = true,
        array $context = [],
    ): int {
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

        $useGrossPrice = $store->getUseGrossPrice();

        $price = $this->carrierPriceCalculator->getPrice($carrier, $shippable, $address, $context);

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
        $cartTaxes = $taxCalculationService->calculateShippingTax($shippable, $carrier, $address, $price, $context);

        $cartTax = array_sum(array_map(static function (TaxItemInterface $taxItem) {
            return $taxItem->getAmount();
        }, $cartTaxes));

        if ($useGrossPrice) {
            return $price - $cartTax;
        }

        return $price + $cartTax;
    }
}
