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

namespace CoreShop\Component\Core\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface as CoreCarrierInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroup;
use Webmozart\Assert\Assert;

class TaxCalculationStrategyTaxRule implements TaxCalculationStrategyInterface
{
    public function __construct(
        private TaxCollectorInterface $taxCollector,
        private TaxCalculatorFactoryInterface $taxCalculationFactory,
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
         */
        Assert::isInstanceOf($shippable, StoreAwareInterface::class);

        $store = $shippable->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        if (!$carrier instanceof CoreCarrierInterface) {
            return [];
        }

        $taxRule = $carrier->getTaxRule();

        if (!$taxRule instanceof TaxRuleGroup) {
            return [];
        }

        $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($taxRule, $address, $context);

        if ($store->getUseGrossPrice()) {
            return $this->taxCollector->collectTaxesFromGross($taxCalculator, $shippingAmount);
        }

        return $this->taxCollector->collectTaxes($taxCalculator, $shippingAmount);
    }
}
