<?php

namespace CoreShop\Bundle\ShippingBundle\Checker;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class CarrierShippingRuleChecker implements CarrierShippingRuleCheckerInterface
{
    /**
     * @var RuleValidationProcessorInterface
     */
    protected $ruleValidationProcessor;

    /**
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     */
    public function __construct(RuleValidationProcessorInterface $ruleValidationProcessor)
    {
        $this->ruleValidationProcessor = $ruleValidationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address)
    {
        $shippingRules = $carrier->getShippingRules();

        foreach ($shippingRules as $rule) {
            if ($this->ruleValidationProcessor->isValid(["carrier" => $carrier, "cart" => $cart, "address" => $address], $rule->getShippingRule())) {
                return $rule;
            }
        }

        return false;
    }
}