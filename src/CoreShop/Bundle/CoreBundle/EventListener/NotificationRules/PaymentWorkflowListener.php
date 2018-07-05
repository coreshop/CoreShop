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

use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class PaymentWorkflowListener extends AbstractNotificationRuleListener
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function setOrderRepository(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Event $event
     */
    public function applyPaymentWorkflowTransitionCompleted(Event $event)
    {
        Assert::isInstanceOf($event->getSubject(), PaymentInterface::class);

        $this->rulesProcessor->applyRules('payment', $event->getSubject(), [
            'order' => $event->getSubject()->getOrder(),
            'paymentState' => $event->getSubject()->getState(),
        ]);
    }
}
