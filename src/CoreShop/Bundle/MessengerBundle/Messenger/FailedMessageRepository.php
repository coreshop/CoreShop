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

use CoreShop\Bundle\MessengerBundle\Exception\ReceiverNotListableException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final class FailedMessageRepository implements FailedMessageRepositoryInterface
{
    public function __construct(
        private FailureReceiversRepositoryInterface $failureReceivers,
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
            /** @var RedeliveryStamp|null $lastRedeliveryStamp */
            $lastRedeliveryStamp = $envelope->last(RedeliveryStamp::class);
            /** @var ErrorDetailsStamp|null $lastErrorDetailsStamp */
            $lastErrorDetailsStamp = $envelope->last(ErrorDetailsStamp::class);

            $rows[] = new FailedMessageDetails(
                $this->getMessageId($envelope),
                $envelope->getMessage()::class,
                null !== $lastRedeliveryStamp ? $lastRedeliveryStamp->getRedeliveredAt()->format('Y-m-d H:i:s') : '',
                null !== $lastErrorDetailsStamp ? $lastErrorDetailsStamp->getExceptionMessage() : '',
                print_r($envelope->getMessage(), true),
            );
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
