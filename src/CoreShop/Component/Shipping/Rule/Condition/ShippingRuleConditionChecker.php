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
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

class ShippingRuleConditionChecker extends AbstractConditionChecker
{
    public function __construct(protected RuleValidationProcessorInterface $ruleValidationProcessor, protected RepositoryInterface $shippingRuleRepository)
    {
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
