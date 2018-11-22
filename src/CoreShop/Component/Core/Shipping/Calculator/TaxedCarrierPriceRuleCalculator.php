<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface as CoreCarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Taxation\TaxApplicatorInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface as BaseCarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use Webmozart\Assert\Assert;

final class TaxedCarrierPriceRuleCalculator implements TaxedShippingCalculatorInterface
{
    /**
     * @var CarrierPriceCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var TaxApplicatorInterface
     */
    private $taxApplicator;

    /**
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     * @param TaxCalculatorFactoryInterface   $taxCalculatorFactory
     * @param TaxApplicatorInterface          $taxApplicator
     */
    public function __construct(
        CarrierPriceCalculatorInterface $carrierPriceCalculator,
        TaxCalculatorFactoryInterface $taxCalculatorFactory,
        TaxApplicatorInterface $taxApplicator
    ) {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->taxApplicator = $taxApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(BaseCarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, $withTax = true)
    {
        /**
         * @var $shippable CartInterface
         */
        Assert::isInstanceOf($shippable, CartInterface::class);

        $price = $this->carrierPriceCalculator->getPrice($carrier, $shippable, $address);

        if (!$carrier instanceof CoreCarrierInterface) {
            return $price;
        }

        $taxCalculator = $this->getTaxCalculator($carrier, $address);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($price, ['store' => $shippable->getStore()], $taxCalculator, $withTax);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    private function getTaxCalculator(CoreCarrierInterface $carrier, AddressInterface $address)
    {
        $taxRuleGroup = $carrier->getTaxRule();

        if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
            return $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
        }

        return null;
    }
}
