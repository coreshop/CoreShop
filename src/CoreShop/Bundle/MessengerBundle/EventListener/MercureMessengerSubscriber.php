<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\MessengerBundle\EventListener;

use CoreShop\Bundle\MessengerBundle\Mercure\MessengerUpdate;
use CoreShop\Bundle\MessengerBundle\Stamp\PimcoreObjectStamp;
use Pimcore\Bundle\StudioBackendBundle\Mercure\Service\PublishServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Mercure\Util\Topics;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

class MercureMessengerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PublishServiceInterface $publishService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => ['onMessageHandled', 100],
            WorkerMessageFailedEvent::class => ['onMessageFailed', 100],
        ];
    }

    public function onMessageHandled(WorkerMessageHandledEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $receiverName = $event->getReceiverName();

        /** @var BusNameStamp|null $busNameStamp */
        $busNameStamp = $envelope->last(BusNameStamp::class);

        // Only handle CoreShop bus messages
        if (null !== $busNameStamp && $busNameStamp->getBusName() !== 'coreshop.bus') {
            return;
        }

        /** @var TransportMessageIdStamp|null $messageIdStamp */
        $messageIdStamp = $envelope->last(TransportMessageIdStamp::class);
        /** @var PimcoreObjectStamp|null $pimcoreObjectStamp */
        $pimcoreObjectStamp = $envelope->last(PimcoreObjectStamp::class);

        $update = MessengerUpdate::handled(
            $receiverName,
            \get_class($envelope->getMessage()),
            $messageIdStamp?->getId() !== null ? (string) $messageIdStamp->getId() : null,
            $pimcoreObjectStamp?->getObjectId(),
        );

        $this->publish($update);
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        // Only publish when message won't be retried
        if ($event->willRetry()) {
            return;
        }

        $envelope = $event->getEnvelope();
        $receiverName = $event->getReceiverName();

        /** @var BusNameStamp|null $busNameStamp */
        $busNameStamp = $envelope->last(BusNameStamp::class);

        // Only handle CoreShop bus messages
        if (null !== $busNameStamp && $busNameStamp->getBusName() !== 'coreshop.bus') {
            return;
        }

        /** @var TransportMessageIdStamp|null $messageIdStamp */
        $messageIdStamp = $envelope->last(TransportMessageIdStamp::class);
        /** @var PimcoreObjectStamp|null $pimcoreObjectStamp */
        $pimcoreObjectStamp = $envelope->last(PimcoreObjectStamp::class);
        /** @var ErrorDetailsStamp|null $errorStamp */
        $errorStamp = $envelope->last(ErrorDetailsStamp::class);

        $update = MessengerUpdate::failed(
            $receiverName,
            \get_class($envelope->getMessage()),
            $messageIdStamp?->getId() !== null ? (string) $messageIdStamp->getId() : null,
            $errorStamp?->getExceptionMessage(),
            $pimcoreObjectStamp?->getObjectId(),
        );

        $this->publish($update);
    }

    private function publish(MessengerUpdate $update): void
    {
        try {
            // Note: We intentionally don't set the SSE 'type' parameter because
            // Pimcore's AbstractMercureProcess uses eventSource.onmessage which
            // only receives events without a type (or type: message).
            // Named SSE events require addEventListener() which Pimcore doesn't use.
            // The eventType is included in the payload (MessengerUpdate) for filtering.
            $this->publishService->publish(
                Topics::STUDIO->value,
                $update,
            );
        } catch (\Throwable) {
            // Silently ignore publish errors to not break message processing
        }
    }
}
