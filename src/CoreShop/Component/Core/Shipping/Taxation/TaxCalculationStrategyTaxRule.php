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

declare(strict_types=1);

namespace CoreShop\Component\Core\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CarrierInterface as CoreCarrierInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroup;
use Webmozart\Assert\Assert;

class TaxCalculationStrategyTaxRule implements TaxCalculationStrategyInterface
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
        ShippableInterface $shippable,
        CarrierInterface $carrier,
        AddressInterface $address,
        int $shippingAmountNet
    ): array {
        /**
         * @var OrderInterface $shippable
         */
        Assert::isInstanceOf($shippable, OrderInterface::class);

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

        $taxCalculator = $this->taxCalculationFactory->getTaxCalculatorForAddress($taxRule, $address);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            if ($store->getUseGrossPrice()) {
                return $this->taxCollector->collectTaxesFromGross($taxCalculator, $shippingAmountNet);
            }

            return $this->taxCollector->collectTaxes($taxCalculator, $shippingAmountNet);
        }

        return [];
    }
}
