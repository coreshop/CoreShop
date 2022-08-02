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
