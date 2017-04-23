<?php

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Rule\Model\ConditionInterface;

interface RuleConditionsValidationProcessorInterface
{
    /**
     * @param $subject
     * @param ConditionInterface[] $conditions
     *
     * @return bool
     */
    public function isValid($subject, $conditions);
}
