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

use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderUpdateListener extends AbstractNotificationRuleListener
{
    public function applyOrderUpdateNotification(GenericEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof \CoreShop\Component\Order\Model\OrderInterface) {
            return;
        }

        $this->rulesProcessor->applyRules($order->getSaleState(), $order, [
            'backend_updated' => true,
        ]);
    }
}
