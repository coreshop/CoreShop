<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
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
     * @param RepositoryInterface $shippingRuleRepository
     */
    public function __construct(RuleValidationProcessorInterface $ruleValidationProcessor, RepositoryInterface $shippingRuleRepository)
    {
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->shippingRuleRepository = $shippingRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $shippingRuleId = $configuration['shippingRule'];
        $shippingRule = $this->shippingRuleRepository->find($shippingRuleId);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->ruleValidationProcessor->isValid(["carrier" => $carrier, "cart" => $cart, "address" => $address], $shippingRule);
        }

        return false;
    }
}
