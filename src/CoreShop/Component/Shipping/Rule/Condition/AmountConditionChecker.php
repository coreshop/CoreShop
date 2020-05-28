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

declare(strict_types=1);

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

class AmountConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration): bool
    {
        $minAmount = $configuration['minAmount'];
        $maxAmount = $configuration['maxAmount'];
        $gross = $configuration['gross'] ?? true;

        $totalAmount = $shippable->getSubtotal($gross);

        if ($minAmount > 0) {
            if ($totalAmount < $minAmount) {
                return false;
            }
        }

        if ($maxAmount > 0) {
            if ($totalAmount > $maxAmount) {
                return false;
            }
        }

        return true;
    }
}
