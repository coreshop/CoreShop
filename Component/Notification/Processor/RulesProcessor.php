<?php

namespace CoreShop\Component\Notification\Processor;

use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class RulesProcessor implements RulesProcessorInterface
{
    /**
     * @var NotificationRuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleValidationProcessorInterface
     */
    private $ruleValidationProcessor;

    /**
     * @var RuleApplierInterface
     */
    private $ruleApplier;

    /**
     * @param NotificationRuleRepositoryInterface $ruleRepository
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     * @param RuleApplierInterface $ruleApplier
     */
    public function __construct(
        NotificationRuleRepositoryInterface $ruleRepository,
        RuleValidationProcessorInterface $ruleValidationProcessor,
        RuleApplierInterface $ruleApplier
    )
    {
        $this->ruleRepository = $ruleRepository;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->ruleApplier = $ruleApplier;
    }

    /**
     * {@inheritdoc}
     */
    public function applyRules($type, $subject, $params = []) {
        $rules = $this->ruleRepository->findForType($type);

        foreach ($rules as $rule) {
            if ($this->ruleValidationProcessor->isValid($subject, $rule)) {
                $this->ruleApplier->applyRule($rule, $subject, $params);
            }
        }
    }
}