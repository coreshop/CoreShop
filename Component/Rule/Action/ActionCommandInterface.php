<?php

namespace CoreShop\Component\Promotion\Action;

use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

interface ActionCommandInterface
{
    /**
     * @param RuleSubjectInterface $subject
     * @param array $configuration
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function execute(RuleSubjectInterface $subject, array $configuration, RuleInterface $rule);

    /**
     * @param RuleSubjectInterface $subject
     * @param array $configuration
     * @param RuleInterface $rule
     *
     * @return mixed
     */
    public function revert(RuleSubjectInterface $subject, array $configuration, RuleInterface $rule);
}
