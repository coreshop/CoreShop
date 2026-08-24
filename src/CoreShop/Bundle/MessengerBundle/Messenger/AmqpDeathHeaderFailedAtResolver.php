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

        // Do not narrow this to int/string: "time" is an AMQP timestamp field, and ext-amqp decodes
        // it into an AMQPTimestamp value object, not a scalar. AMQPTimestamp is \Stringable and
        // stringifies to the epoch seconds. Other AMQP clients hand over a plain int instead, so
        // both have to be accepted here.
        if ($time instanceof \Stringable) {
            $time = (string) $time;
        }

        if (!is_int($time) && !(is_string($time) && is_numeric($time))) {
            return null;
        }

        $failedAt = \DateTimeImmutable::createFromFormat('U', (string) (int) $time);

        if (false === $failedAt) {
            return null;
        }

        // Keep the setTimezone() call: createFromFormat('U', ...) always returns the date in UTC,
        // whereas RedeliveryStampFailedAtResolver returns server local time. FailedMessageRepository
        // formats whatever it gets with a plain format('Y-m-d H:i:s') and no conversion, so without
        // this normalisation broker dead-letterings would be rendered in a different timezone than
        // Symfony side failures in the very same grid.
        return $failedAt->setTimezone(new \DateTimeZone(date_default_timezone_get()));
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
