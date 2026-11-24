<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Shipping\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Recursion\RuleRecursionGuard;
use CoreShop\Component\Rule\Recursion\RuleRecursionGuardInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Shipping\Rule\Action\CarrierPriceActionProcessorInterface;
use CoreShop\Component\Shipping\Rule\Action\CarrierPriceModificationActionProcessorInterface;
use CoreShop\Component\Shipping\Rule\Processor\ShippingRuleActionProcessorInterface;

class ShippingRuleActionProcessor implements ShippingRuleActionProcessorInterface
{
    public function __construct(
        protected ServiceRegistryInterface $actionServiceRegistry,
        protected RuleRecursionGuardInterface $recursionGuard = new RuleRecursionGuard(),
    ) {
    }

    public function getPrice(
        ShippingRuleInterface $shippingRule,
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        array $context,
    ): int {
        // A rule that ends up calculating its own price again (e.g. through a "shippingRule" action) adds nothing
        return $this->recursionGuard->guard(
            RuleRecursionGuardInterface::SCOPE_ACTIONS_PRICE,
            $shippingRule,
            $carrier,
            fn (): int => $this->calculatePrice($shippingRule, $carrier, $shippable, $address, $context),
            0,
        );
    }

    public function getModification(
        ShippingRuleInterface $shippingRule,
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        int $price,
        array $context,
    ): int {
        return $this->recursionGuard->guard(
            RuleRecursionGuardInterface::SCOPE_ACTIONS_MODIFICATION,
            $shippingRule,
            $carrier,
            fn (): int => $this->calculateModification($shippingRule, $carrier, $shippable, $address, $price, $context),
            0,
        );
    }

    protected function calculatePrice(
        ShippingRuleInterface $shippingRule,
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        array $context,
    ): int {
        $price = 0;

        foreach ($shippingRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof CarrierPriceActionProcessorInterface) {
                $price += $processor->getPrice(
                    $carrier,
                    $shippable,
                    $address,
                    $action->getConfiguration(),
                    $context,
                );
            }
        }

        return $price;
    }

    protected function calculateModification(
        ShippingRuleInterface $shippingRule,
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        int $price,
        array $context,
    ): int {
        $modifications = 0;

        foreach ($shippingRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof CarrierPriceModificationActionProcessorInterface) {
                $modifications += $processor->getModification(
                    $carrier,
                    $shippable,
                    $address,
                    $price,
                    $action->getConfiguration(),
                    $context,
                );
            }
        }

        return $modifications;
    }
}
