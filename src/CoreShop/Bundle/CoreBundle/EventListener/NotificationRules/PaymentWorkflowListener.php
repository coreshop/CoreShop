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

use CoreShop\Component\Core\Model\PaymentInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class PaymentWorkflowListener extends AbstractNotificationRuleListener
{
    public function applyPaymentWorkflowTransitionCompleted(Event $event): void
    {
        $subject = $event->getSubject();

        /**
         * @var PaymentInterface $subject
         */
        Assert::isInstanceOf($subject, PaymentInterface::class);

        $this->rulesProcessor->applyRules('payment', $subject, [
            'order_id' => $event->getSubject()->getOrder()->getId(),
            'paymentState' => $event->getSubject()->getState(),
        ]);
    }
}
