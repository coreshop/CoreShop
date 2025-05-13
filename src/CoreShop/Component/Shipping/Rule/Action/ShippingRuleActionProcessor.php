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

namespace CoreShop\Component\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Shipping\Rule\Processor\ShippingRuleActionProcessorInterface;

class ShippingRuleActionProcessor implements CarrierPriceActionProcessorInterface, CarrierPriceModificationActionProcessorInterface
{
    public function __construct(
        protected ShippingRuleActionProcessorInterface $shippingRuleProcessor,
        protected RepositoryInterface $shippingRuleRepository,
    ) {
    }

    public function getPrice(
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        array $configuration,
        array $context,
    ): int {
        $shippingRule = $this->shippingRuleRepository->find($configuration['shippingRule']);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->shippingRuleProcessor->getPrice($shippingRule, $carrier, $shippable, $address, $context);
        }

        return 0;
    }

    public function getModification(
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        int $price,
        array $configuration,
        array $context,
    ): int {
        $shippingRule = $this->shippingRuleRepository->find($configuration['shippingRule']);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->shippingRuleProcessor->getModification($shippingRule, $carrier, $shippable, $address, $price, $context);
        }

        return 0;
    }
}
