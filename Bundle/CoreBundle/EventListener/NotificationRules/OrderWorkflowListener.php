<?php

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Workflow\ProposalWorkflowEvent;

final class OrderWorkflowListener extends AbstractNotificationRuleListener
{
    public function applyRule(ProposalWorkflowEvent $event)
    {
        if ($event->getProposal() instanceof OrderInterface) {
            $this->rulesProcessor->applyRules('invoice', $event->getProposal(), [
                'fromState' => $event->getOldState(),
                'toState' => $event->getNewState()
            ]);
        }
    }
}
