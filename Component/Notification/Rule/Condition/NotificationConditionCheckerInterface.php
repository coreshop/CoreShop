<?php

namespace CoreShop\Component\Notification\Rule\Condition;

use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;

interface NotificationConditionCheckerInterface extends ConditionCheckerInterface
{
    /**
     * @param $subject
     * @param $params
     * @param array $configuration
     * @return boolean
     */
    public function isNotificationRuleValid($subject, $params, array $configuration);
}
