<?php

namespace CoreShop\Component\Rule\Action;

use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

interface RuleApplicatorInterface
{
    /**
     * @param RuleSubjectInterface $subject
     * @param RuleInterface $rule
     */
    public function apply(RuleSubjectInterface $subject, RuleInterface $rule);

    /**
     * @param RuleSubjectInterface $subject
     * @param RuleInterface $rule
     */
    public function revert(RuleSubjectInterface $subject, RuleInterface $rule);
}
