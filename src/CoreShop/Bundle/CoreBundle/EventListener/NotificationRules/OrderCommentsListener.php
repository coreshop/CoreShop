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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Pimcore\Model\Element\Note;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class OrderCommentsListener extends AbstractNotificationRuleListener
{
    private OrderRepositoryInterface $orderRepository;

    public function setOrderRepository(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

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
