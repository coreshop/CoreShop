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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\NotificationBundle\EventListener;

use CoreShop\Bundle\MessengerBundle\Stamp\PimcoreObjectStamp;
use CoreShop\Component\Notification\Messenger\NotificationMessage;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class MessengerListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageReceivedEvent::class => ['onMessageReceived'],
        ];
    }

    public function onMessageReceived(WorkerMessageReceivedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();

        if (!$message instanceof NotificationMessage) {
            return;
        }

        if (is_subclass_of($message->getResourceType(), Concrete::class)) {
            $event->addStamps(new PimcoreObjectStamp($message->getResourceId()));
        }
    }
}
