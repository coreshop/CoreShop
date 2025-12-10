<?php
declare(strict_types=1);

namespace CoreShop\Bundle\MessengerBundle\Event;

use CoreShop\Bundle\MessengerBundle\Messenger\FailedMessageDetails;
use Symfony\Component\Messenger\Envelope;
use Symfony\Contracts\EventDispatcher\Event;

final class FailedMessageDetailsEvent extends Event
{
    public function __construct(
        private string $receiverName,
        private Envelope $envelope,
        private FailedMessageDetails $failedMessageDetails,
    ) {
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

    public function getFailedMessageDetails(): FailedMessageDetails
    {
        return $this->failedMessageDetails;
    }
}
