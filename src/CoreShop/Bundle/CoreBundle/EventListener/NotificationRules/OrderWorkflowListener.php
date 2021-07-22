<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class OrderWorkflowListener extends AbstractNotificationRuleListener
{
    /**
     * @param Event $event
     */
    public function applyOrderWorkflowRule(Event $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

        $customer = $order->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->rulesProcessor->applyRules('order', $order, [
            'workflow' => $event->getWorkflowName(),
            'fromState' => $event->getMarking()->getPlaces(),
            'toState' => $event->getTransition()->getTos(),
            '_locale' => $order->getLocaleCode(),
            'recipient' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'orderNumber' => $order->getOrderNumber(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }
}
