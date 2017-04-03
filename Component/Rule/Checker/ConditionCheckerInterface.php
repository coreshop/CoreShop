<?php

namespace CoreShop\Component\Rule\Checker;

use CoreShop\Component\Rule\Model\RuleSubjectInterface;

interface ConditionCheckerInterface
{
    /**
     * @param RuleSubjectInterface $subject
     * @param array $configuration
     * @return mixed
     */
    public function isValid(RuleSubjectInterface $subject, array $configuration);
}