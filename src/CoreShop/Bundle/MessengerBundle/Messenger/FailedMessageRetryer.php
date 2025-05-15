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
use CoreShop\Bundle\MessengerBundle\Stamp\RetriedByUserStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final class FailedMessageRetryer implements FailedMessageRetryerInterface
{
    public function __construct(
        private FailureReceiversRepositoryInterface $failureReceivers,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function retryFailedMessage(string $receiver, int $id): void
    {
        $failureReceiver = $this->failureReceivers->getFailureReceiver($receiver);

        if (!$failureReceiver instanceof ListableReceiverInterface) {
            throw new ReceiverNotListableException();
        }

        $envelope = $failureReceiver->find($id);
        if (null === $envelope) {
            throw new \RuntimeException(sprintf('The message "%s" was not found.', $id));
        }

        /**
         * @var BusNameStamp|null $busNameStamp
         */
        $busNameStamp = $envelope->last(BusNameStamp::class);

        if (null === $busNameStamp) {
            throw new \RuntimeException(sprintf('The message "%s" has no Bus Name set.', $id));
        }

        $newEnvelope = new Envelope($envelope->getMessage(), [
            new RetriedByUserStamp(),
            new BusNameStamp($busNameStamp->getBusName()),
        ]);

        $this->messageBus->dispatch($newEnvelope);

        $failureReceiver->reject($envelope);
    }
}
