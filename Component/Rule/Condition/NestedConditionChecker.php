<?php

namespace CoreShop\Component\Rule\Condition;

class NestedConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var RuleConditionsValidationProcessorInterface
     */
    protected $ruleConditionsValidationProcessor;

    /**
     * @param RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor
     */
    public function __construct(RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor)
    {
        $this->ruleConditionsValidationProcessor = $ruleConditionsValidationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        $operator = $configuration['operator'];

        foreach ($configuration['conditions'] as $condition) {
            $valid = $this->ruleConditionsValidationProcessor->isValid($subject, [$condition]);

            if ($operator === "and") {
                if (!$valid) {
                    return false;
                }
            } elseif ($operator === "or") {
                if ($valid) {
                    return true;
                }
            }
        }

        return true;
    }
}
