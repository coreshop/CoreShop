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

namespace CoreShop\Bundle\MessengerBundle\Messenger;

use CoreShop\Bundle\MessengerBundle\Event\MessageDetailsEvent;
use CoreShop\Bundle\MessengerBundle\Exception\ReceiverNotListableException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final class MessageRepository implements MessageRepositoryInterface
{
    private const SECONDS_PER_MINUTE = 60;

    private const SECONDS_PER_HOUR = 3600;

    private const SECONDS_PER_DAY = 86400;

    public function __construct(
        private ReceiversRepositoryInterface $receivers,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function listMessages(string $receiverName, int $limit = 10): array
    {
        $receiver = $this->receivers->getReceiver($receiverName);

        if (!$receiver instanceof ListableReceiverInterface) {
            throw new ReceiverNotListableException();
        }

        $envelopes = $receiver->all($limit);

        $rows = [];
        /**
         * @var Envelope $envelope
         */
        foreach ($envelopes as $envelope) {
            $messageDetails = new MessageDetails(
                $this->getMessageId($envelope),
                $envelope->getMessage()::class,
                '<pre>' . print_r($envelope->getMessage(), true) . '</pre>',
                $this->getDelayInfo($envelope),
            );

            /** @var MessageDetailsEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new MessageDetailsEvent($receiverName, $envelope, $messageDetails),
                'coreshop.messenger.message_details',
            );

            $rows[] = $event->getMessageDetails();
        }

        return $rows;
    }

    private function getMessageId(Envelope $envelope): mixed
    {
        /** @var TransportMessageIdStamp|null $stamp */
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        return $stamp?->getId();
    }

    private function getDelayInfo(Envelope $envelope): ?string
    {
        /** @var DelayStamp|null $delayStamp */
        $delayStamp = $envelope->last(DelayStamp::class);

        if (null === $delayStamp) {
            return null;
        }

        $delayMs = $delayStamp->getDelay();
        $delaySeconds = (int) ($delayMs / 1000);

        if ($delaySeconds < self::SECONDS_PER_MINUTE) {
            return sprintf('%d %s delay', $delaySeconds, $delaySeconds === 1 ? 'second' : 'seconds');
        }

        if ($delaySeconds < self::SECONDS_PER_HOUR) {
            $minutes = (int) ($delaySeconds / self::SECONDS_PER_MINUTE);

            return sprintf('%d %s delay', $minutes, $minutes === 1 ? 'minute' : 'minutes');
        }

        if ($delaySeconds < self::SECONDS_PER_DAY) {
            $hours = (int) ($delaySeconds / self::SECONDS_PER_HOUR);

            return sprintf('%d %s delay', $hours, $hours === 1 ? 'hour' : 'hours');
        }

        $days = (int) ($delaySeconds / self::SECONDS_PER_DAY);

        return sprintf('%d %s delay', $days, $days === 1 ? 'day' : 'days');
    }
}
