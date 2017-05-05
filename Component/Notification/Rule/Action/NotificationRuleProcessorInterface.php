<?php

namespace CoreShop\Component\Notification\Rule\Action;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;

interface NotificationRuleProcessorInterface
{
    /**
     * @param $subject
     * @param NotificationRuleInterface $rule
     * @param array $configuration
     * @param array $params
     * @return mixed
     */
    public function apply($subject, NotificationRuleInterface $rule, array $configuration, $params = []);
}
