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
use CoreShop\Component\Core\Model\OrderInterface;
use Pimcore\Model\Element\Note;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class OrderCommentsListener extends AbstractNotificationRuleListener
{
    public function applyOrderCommentAddedNotifications(GenericEvent $event): void
    {
        Assert::isInstanceOf($event->getSubject(), Note::class);

        $order = $event->getArgument('order');
        if (!$order instanceof OrderInterface) {
            return;
        }

        $customer = $order->getCustomer();
        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->rulesProcessor->applyRules('order', $order, [
            'type' => 'create',
            'submitAsEmail' => $event->getArgument('submitAsEmail'),
            'comment' => $event->getSubject(),
            '_locale' => $order->getLocaleCode(),
            'recipient' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'orderNumber' => $order->getOrderNumber(),
        ]);
    }
}
