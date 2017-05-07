<?php

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class InvoiceListener extends AbstractNotificationRuleListener
{
    public function applyRule(GenericEvent $event)
    {
        Assert::isInstanceOf($event->getSubject(), OrderInvoiceInterface::class);

        $this->rulesProcessor->applyRules('invoice', $event->getSubject());
    }
}
