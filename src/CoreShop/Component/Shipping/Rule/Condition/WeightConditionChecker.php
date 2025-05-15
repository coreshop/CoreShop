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

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

class WeightConditionChecker extends AbstractConditionChecker
{
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration): bool
    {
        $minWeight = $configuration['minWeight'];
        $maxWeight = $configuration['maxWeight'];
        $totalWeight = $shippable->getWeight();

        if ($minWeight > 0) {
            if ($totalWeight < $minWeight) {
                return false;
            }
        }

        if ($maxWeight > 0) {
            if ($totalWeight > $maxWeight) {
                return false;
            }
        }

        return true;
    }
}
