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

namespace CoreShop\Component\Shipping\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Shipping\Rule\Processor\ShippingRuleActionProcessorInterface;

class CarrierShippingRulePriceCalculator implements CarrierPriceCalculatorInterface
{
    public function __construct(protected CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker, protected ShippingRuleActionProcessorInterface $shippingRuleProcessor)
    {
    }

    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $context): int
    {
        /**
         * First valid price rule wins. so, we loop through all ShippingRuleGroups
         * get the first valid one, and process it for the price.
         */
        $shippingRule = $this->carrierShippingRuleChecker->findValidShippingRule($carrier, $shippable, $address);

        if ($shippingRule instanceof ShippingRuleInterface) {
            $price = $this->shippingRuleProcessor->getPrice(
                $shippingRule,
                $carrier,
                $shippable,
                $address,
                $context
            );
            $modifications = $this->shippingRuleProcessor->getModification(
                $shippingRule,
                $carrier,
                $shippable,
                $address,
                $price,
                $context
            );

            return $price + $modifications;
        }

        return 0;
    }
}
