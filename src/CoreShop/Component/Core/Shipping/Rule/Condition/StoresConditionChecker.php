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

namespace CoreShop\Component\Core\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Store\Model\StoreAwareInterface;

final class StoresConditionChecker extends AbstractConditionChecker
{
    public function isShippingRuleValid(
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        array $configuration,
    ): bool {
        if (!$shippable instanceof StoreAwareInterface) {
            return false;
        }

        return in_array($shippable->getStore()->getId(), $configuration['stores']);
    }
}
