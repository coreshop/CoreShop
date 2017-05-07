<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\Shipment;

use CoreShop\Component\Order\Model\OrderShipmentInterface;

class ShipmentStateChecker extends \CoreShop\Component\Core\Notification\Rule\Condition\Order\ShipmentStateChecker
{
    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof OrderShipmentInterface) {
            return parent::isNotificationRuleValid($subject->getOrder(), $params, $configuration);
        }

        return false;
    }
}