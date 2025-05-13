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

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;

abstract class AbstractConditionChecker implements ShippingConditionCheckerInterface
{
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, array $params = []): bool
    {
        if (!$subject instanceof CarrierInterface) {
            throw new \InvalidArgumentException('Shipping Rule Condition $subject needs to be an array with values shippable, address and carrier');
        }

        if (!array_key_exists('shippable', $params) || !array_key_exists('address', $params)) {
            throw new \InvalidArgumentException('Shipping Rule Condition $subject needs to be an array with values shippable, address and carrier');
        }

        return $this->isShippingRuleValid($subject, $params['shippable'], $params['address'], $configuration);
    }
}
