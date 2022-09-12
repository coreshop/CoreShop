<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderSaleStates;
use Symfony\Component\Workflow\Event\Event;

final class OrderWorkflowListener extends AbstractNotificationRuleListener
{
    public function applyOrderWorkflowRule(Event $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

        if (!in_array($order->getSaleState(), [OrderSaleStates::STATE_QUOTE, OrderSaleStates::STATE_ORDER], true)) {
            return;
        }

        $customer = $order->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->rulesProcessor->applyRules($order->getSaleState(), $order, [
            'workflow' => $event->getWorkflowName(),
            'fromState' => $event->getMarking()->getPlaces(),
            'toState' => $event->getTransition()->getTos(),
            '_locale' => $order->getLocaleCode(),
            'recipient' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'orderNumber' => $order->getOrderNumber(),
            'quoteNumber' => $order->getQuoteNumber(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }
}
