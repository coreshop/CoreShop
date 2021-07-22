<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Shipping\Checker;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

class CarrierShippingRuleChecker implements CarrierShippingRuleCheckerInterface
{
    protected RuleValidationProcessorInterface $ruleValidationProcessor;

    public function __construct(RuleValidationProcessorInterface $ruleValidationProcessor)
    {
        $this->ruleValidationProcessor = $ruleValidationProcessor;
    }

    public function findValidShippingRule(
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address
    ): ?ShippingRuleInterface {
        $shippingRules = $carrier->getShippingRules();

        if (count($shippingRules) === 0) {
            return null;
        }

        foreach ($shippingRules as $rule) {
            $isValid = $this->ruleValidationProcessor->isValid($carrier, $rule instanceof ShippingRuleInterface ? $rule : $rule->getShippingRule(), [
                $carrier,
                'shippable' => $shippable,
                'address' => $address,
            ]);

            if ($isValid === false && $rule->getStopPropagation() === true) {
                return null;
            }

            if ($isValid === true) {
                return $rule instanceof ShippingRuleInterface ? $rule : $rule->getShippingRule();
            }
        }

        return null;
    }
}
