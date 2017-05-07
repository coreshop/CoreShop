<?php

namespace CoreShop\Component\Notification\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Order\Model\CartInterface;
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
