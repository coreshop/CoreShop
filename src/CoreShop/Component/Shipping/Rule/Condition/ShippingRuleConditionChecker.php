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

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

class ShippingRuleConditionChecker extends AbstractConditionChecker
{
    /**
     * @var RuleValidationProcessorInterface
     */
    protected $ruleValidationProcessor;

    /**
     * @var RepositoryInterface
     */
    protected $shippingRuleRepository;

    /**
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     * @param RepositoryInterface              $shippingRuleRepository
     */
    public function __construct(RuleValidationProcessorInterface $ruleValidationProcessor, RepositoryInterface $shippingRuleRepository)
    {
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->shippingRuleRepository = $shippingRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration)
    {
        $shippingRuleId = $configuration['shippingRule'];
        $shippingRule = $this->shippingRuleRepository->find($shippingRuleId);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->ruleValidationProcessor->isValid($carrier, $shippingRule, ['carrier' => $carrier, 'shippable' => $shippable, 'address' => $address]);
        }

        return false;
    }
}
