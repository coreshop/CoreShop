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

namespace CoreShop\Component\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Rule\Processor\ShippingRuleActionProcessorInterface;

class CarrierShippingRulePriceCalculator implements CarrierPriceCalculatorInterface
{
    protected $carrierShippingRuleChecker;
    protected $shippingRuleProcessor;

    public function __construct(
        CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker,
        ShippingRuleActionProcessorInterface $shippingRuleProcessor
    ) {
        $this->carrierShippingRuleChecker = $carrierShippingRuleChecker;
        $this->shippingRuleProcessor = $shippingRuleProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address): int
    {
        /**
         * First valid price rule wins. so, we loop through all ShippingRuleGroups
         * get the first valid one, and process it for the price.
         */
        $shippingRuleGroup = $this->carrierShippingRuleChecker->findValidShippingRule($carrier, $shippable, $address);

        if ($shippingRuleGroup instanceof ShippingRuleGroupInterface) {
            $price = $this->shippingRuleProcessor->getPrice($shippingRuleGroup->getShippingRule(), $carrier, $shippable, $address);
            $modifications = $this->shippingRuleProcessor->getModification($shippingRuleGroup->getShippingRule(), $carrier, $shippable, $address, $price);

            return $price + $modifications;
        }

        return 0;
    }
}
