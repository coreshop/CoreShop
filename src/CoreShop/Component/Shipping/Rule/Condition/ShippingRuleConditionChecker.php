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
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

class ShippingRuleConditionChecker extends AbstractConditionChecker
{
    public function __construct(
        protected RuleValidationProcessorInterface $ruleValidationProcessor,
        protected RepositoryInterface $shippingRuleRepository,
    ) {
    }

    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration): bool
    {
        $shippingRuleId = $configuration['shippingRule'];
        $shippingRule = $this->shippingRuleRepository->find($shippingRuleId);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->ruleValidationProcessor->isValid($carrier, $shippingRule, ['carrier' => $carrier, 'shippable' => $shippable, 'address' => $address]);
        }

        return false;
    }
}
