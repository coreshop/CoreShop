<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

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
                'toState' => $event->getNewState(),
            ]);
        }
    }
}
