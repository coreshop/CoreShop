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

namespace CoreShop\Component\Shipping\Checker;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

class CarrierShippingRuleChecker implements CarrierShippingRuleCheckerInterface
{
    public function __construct(
        protected RuleValidationProcessorInterface $ruleValidationProcessor,
    ) {
    }

    public function findValidShippingRule(
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
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

            if ($isValid === false && ($rule instanceof ShippingRuleGroupInterface && $rule->getStopPropagation() === true)) {
                return null;
            }

            if ($isValid === true) {
                return $rule instanceof ShippingRuleInterface ? $rule : $rule->getShippingRule();
            }
        }

        return null;
    }
}
