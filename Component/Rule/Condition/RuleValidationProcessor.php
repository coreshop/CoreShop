<?php

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleValidationProcessor implements RuleValidationProcessorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $ruleRegistry;

    /**
     * @param ServiceRegistryInterface $ruleRegistry
     */
    public function __construct(ServiceRegistryInterface $ruleRegistry)
    {
        $this->ruleRegistry = $ruleRegistry;
    }

    /**
     * @param $subject
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function isValid($subject, RuleInterface $rule)
    {
        if (!$rule->hasConditions()) {
            return true;
        }

        foreach ($rule->getConditions() as $condition) {
            if (!$this->isConditionValid($subject, $condition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $subject
     * @param ConditionInterface $condition
     *
     * @return bool
     */
    protected function isConditionValid($subject, ConditionInterface $condition)
    {
        /** @var ConditionCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($condition->getType());

        return $checker->isValid($subject, $condition->getConfiguration());
    }
}
