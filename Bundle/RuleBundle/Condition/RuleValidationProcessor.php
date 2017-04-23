<?php

namespace CoreShop\Component\RuleBundle\Condition;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleValidationProcessor implements RuleValidationProcessorInterface
{
    /**
     * @var RuleConditionsValidationProcessor
     */
    private $ruleConditionsValidationProcessor;

    /**
     * @param RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor
     */
    public function __construct(RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor)
    {
        $this->ruleConditionsValidationProcessor = $ruleConditionsValidationProcessor;
    }

    /**
     * @param $subject
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function isValid($subject, RuleInterface $rule)
    {
        return $this->ruleConditionsValidationProcessor->isValid($subject, $rule->getConditions());
    }
}
