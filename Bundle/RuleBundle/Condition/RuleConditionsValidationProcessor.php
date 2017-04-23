<?php

namespace CoreShop\Component\RuleBundle\Condition;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleConditionsValidationProcessor implements RuleConditionsValidationProcessorInterface
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
     * {@inheritdoc}
     */
    public function isValid($subject, $conditions)
    {
        if (!count($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            if (!$this->isConditionValid($subject, $condition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function isConditionValid($subject, ConditionInterface $condition)
    {
        /** @var ConditionCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($condition->getType());

        return $checker->isValid($subject, $condition->getConfiguration());
    }
}
