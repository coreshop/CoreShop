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

use Symfony\Component\Messenger\Envelope;

/**
 * Messages that end up in an AMQP queue because the broker itself dead-lettered them never pass
 * through Symfony's retry/failure handling, so they carry no RedeliveryStamp. RabbitMQ records the
 * moment of every dead-lettering in the "x-death" header, which is the only timestamp available for
 * those messages.
 *
 * Only "rejected" entries are considered: delayed messages are dead-lettered as well (that is how
 * the delay queue works) and would otherwise look like failures on their very first dispatch.
 */
final class AmqpDeathHeaderFailedAtResolver implements FailedAtResolverInterface
{
    public function resolve(Envelope $envelope): ?\DateTimeInterface
    {
        $death = $this->getLastRejectedDeath($envelope);

        if (null === $death) {
            return null;
        }

        $time = $death['time'] ?? null;

        if ($time instanceof \DateTimeInterface) {
            return $time;
        }

        if (!is_int($time) && !is_string($time)) {
            return null;
        }

        return \DateTimeImmutable::createFromFormat('U', (string) $time) ?: null;
    }

    private function getLastRejectedDeath(Envelope $envelope): ?array
    {
        $amqpEnvelope = $this->getAmqpEnvelope($envelope);

        if (null === $amqpEnvelope) {
            return null;
        }

        $deaths = $amqpEnvelope->getHeader('x-death');

        if (!is_array($deaths)) {
            return null;
        }

        foreach ($deaths as $death) {
            if (is_array($death) && 'rejected' === ($death['reason'] ?? null)) {
                return $death;
            }
        }

        return null;
    }

    /**
     * Resolved by duck typing so that the bundle keeps working without symfony/amqp-messenger
     * installed, and with third party AMQP transports that expose their own received stamp.
     */
    private function getAmqpEnvelope(Envelope $envelope): ?object
    {
        foreach ($envelope->all() as $stamps) {
            foreach ($stamps as $stamp) {
                if (!method_exists($stamp, 'getAmqpEnvelope')) {
                    continue;
                }

                $amqpEnvelope = $stamp->getAmqpEnvelope();

                if (is_object($amqpEnvelope) && method_exists($amqpEnvelope, 'getHeader')) {
                    return $amqpEnvelope;
                }
            }
        }

        return null;
    }
}
