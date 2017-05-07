<?php

namespace CoreShop\Component\Notification\Rule\Condition;

abstract class AbstractConditionChecker implements NotificationConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        if (!is_array($subject)) {
            throw new \InvalidArgumentException('Notification Rule Condition $subject needs to be an array with values subject and params');
        }

        if (!array_key_exists('subject', $subject) || !array_key_exists('params', $subject)) {
            throw new \InvalidArgumentException('Notification Rule Condition $subject needs to be an array with values subject and params');
        }

        return $this->isNotificationRuleValid($subject['subject'], $subject['params'], $configuration);
    }

}
