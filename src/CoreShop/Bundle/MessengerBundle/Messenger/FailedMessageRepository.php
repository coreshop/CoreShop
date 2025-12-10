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

namespace CoreShop\Bundle\MessengerBundle\Messenger;

use CoreShop\Bundle\MessengerBundle\Event\FailedMessageDetailsEvent;
use CoreShop\Bundle\MessengerBundle\Exception\ReceiverNotListableException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final class FailedMessageRepository implements FailedMessageRepositoryInterface
{
    public function __construct(
        private FailureReceiversRepositoryInterface $failureReceivers,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function listFailedMessages(string $receiverName, int $limit = 10): array
    {
        $receiver = $this->failureReceivers->getFailureReceiver($receiverName);

        if (!$receiver instanceof ListableReceiverInterface) {
            throw new ReceiverNotListableException();
        }

        $envelopes = $receiver->all($limit);

        $rows = [];
        foreach ($envelopes as $envelope) {
            $lastRedeliveryStamp = $envelope->last(RedeliveryStamp::class);
            $lastErrorDetailsStamp = $envelope->last(ErrorDetailsStamp::class);

            $failedMessageDetails = new FailedMessageDetails(
                $this->getMessageId($envelope),
                $envelope->getMessage()::class,
                ($lastRedeliveryStamp?->getRedeliveredAt()->format('Y-m-d H:i:s')) ?? '',
                $lastErrorDetailsStamp?->getExceptionMessage(),
                '<pre>' . print_r($envelope->getMessage(), true) . '</pre>',
            );

            /** @var FailedMessageDetailsEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new FailedMessageDetailsEvent($receiverName, $envelope, $failedMessageDetails),
                'coreshop.messenger.failed_message_details',
            );

            $rows[] = $event->getFailedMessageDetails();
        }

        return $rows;
    }

    private function getMessageId(Envelope $envelope): mixed
    {
        /** @var TransportMessageIdStamp|null $stamp */
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        return $stamp?->getId();
    }
}
