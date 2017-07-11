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

namespace CoreShop\Bundle\CoreBundle\Shipping\Calculator;

use CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface as CoreCarrierInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface as BaseCarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

final class TaxedCarrierPriceRuleCalculator implements TaxedShippingCalculatorInterface
{
    /**
     * @var CarrierPriceCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @var TaxCalculatorInterface
     */
    private $taxCalculator;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(
        CarrierPriceCalculatorInterface $carrierPriceCalculator,
        TaxCalculatorFactoryInterface $taxCalculatorFactory
    ) {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(BaseCarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, $withTax = true)
    {
        $netPrice = $this->carrierPriceCalculator->getPrice($carrier, $shippable, $address);

        if ($withTax && $carrier instanceof CoreCarrierInterface) {
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
    private function getTaxCalculator(CoreCarrierInterface $carrier, AddressInterface $address)
    {
        if (is_null($this->taxCalculator)) {
            $taxRuleGroup = $carrier->getTaxRule();

            if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
                $this->taxCalculator = $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
            } else {
                $this->taxCalculator = null;
            }
        }

        return $this->taxCalculator;
    }
}
