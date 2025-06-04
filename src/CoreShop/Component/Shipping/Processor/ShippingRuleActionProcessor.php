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

namespace CoreShop\Component\Shipping\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
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
    ) {
    }

    public function getPrice(
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

    public function getModification(
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
