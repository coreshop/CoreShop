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

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Workflow\ProposalWorkflowEvent;

final class OrderWorkflowListener extends AbstractNotificationRuleListener
{
    public function applyRule(ProposalWorkflowEvent $event)
    {
        $order = $event->getProposal();

        if ($order instanceof OrderInterface) {
            $customer = $order->getCustomer();

            if ($customer instanceof CustomerInterface) {
                $this->rulesProcessor->applyRules('order', $event->getProposal(), [
                    'fromState' => $event->getOldState(),
                    'toState' => $event->getNewState(),
                    '_locale' => $order->getOrderLanguage(),
                    'recipient' => $customer->getEmail(),
                    'firstname' => $customer->getFirstname(),
                    'lastname' => $customer->getLastname(),
                    'orderNumber' => $order->getOrderNumber()
                ]);
            }
        }
    }
}
