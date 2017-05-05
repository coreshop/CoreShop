<?php

namespace CoreShop\Component\Notification\Processor;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;

interface RuleApplierInterface
{
    /**
     * Apply all actions from a rule
     *
     * @param NotificationRuleInterface $rule
     * @param $subject
     * @param $params
     * @return mixed
     */
    public function applyRule(NotificationRuleInterface $rule, $subject, $params);
}