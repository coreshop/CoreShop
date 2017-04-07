<?php

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Rule\Model\RuleInterface;

interface RuleValidationProcessorInterface
{
    /**
     * @param $subject
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function isValid($subject, RuleInterface $rule);
}
