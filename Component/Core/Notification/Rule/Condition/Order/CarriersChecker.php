<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\Order;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderInterface;

class CarriersChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof OrderInterface) {
            if ($subject->getCarrier() instanceof CarrierInterface) {
                if (in_array($subject->getCarrier()->getId(), $configuration['carriers'])) {
                    return true;
                }
            }
        }

        return false;
    }
}