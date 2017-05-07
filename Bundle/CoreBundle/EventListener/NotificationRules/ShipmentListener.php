<?php

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Order\Model\OrderShipmentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class ShipmentListener extends AbstractNotificationRuleListener
{
    public function applyRule(GenericEvent $event)
    {
        Assert::isInstanceOf($event->getSubject(), OrderShipmentInterface::class);

        $this->rulesProcessor->applyRules('shipment', $event->getSubject());
    }
}
