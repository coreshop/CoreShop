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
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final class MessageRepository implements MessageRepositoryInterface
{
    public function __construct(
        private ReceiversRepositoryInterface $receivers,
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
            $rows[] = new MessageDetails(
                $this->getMessageId($envelope),
                $envelope->getMessage()::class,
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
