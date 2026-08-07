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

use CoreShop\Bundle\MessengerBundle\Exception\ReceiverNotListableException;
use CoreShop\Bundle\MessengerBundle\Mercure\MessengerUpdate;
use Pimcore\Bundle\StudioBackendBundle\Mercure\Service\PublishServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Mercure\Util\Topics;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final class FailedMessageRejecter implements FailedMessageRejecterInterface
{
    public function __construct(
        private FailureReceiversRepositoryInterface $failureReceivers,
        private ?PublishServiceInterface $publishService = null,
    ) {
    }

    public function rejectStoredMessage(string $receiverName, int $id): void
    {
        $failureReceiver = $this->failureReceivers->getFailureReceiver($receiverName);

        if (!$failureReceiver instanceof ListableReceiverInterface) {
            throw new ReceiverNotListableException();
        }

        $envelope = $failureReceiver->find($id);

        if (null === $envelope) {
            throw new \RuntimeException(sprintf('The message with id "%s" was not found.', $id));
        }

        $failureReceiver->reject($envelope);

        $this->publishMercureUpdate($receiverName, $envelope, (string) $id);
    }

    private function publishMercureUpdate(string $receiver, Envelope $envelope, string $messageId): void
    {
        if (null === $this->publishService) {
            return;
        }

        try {
            $update = MessengerUpdate::rejected(
                $receiver,
                \get_class($envelope->getMessage()),
                $messageId,
            );

            $this->publishService->publish(
                Topics::STUDIO->value,
                $update,
                true,
                null,
                'coreshop.messenger.update',
            );
        } catch (\Throwable) {
            // Silently ignore publish errors
        }
    }
}
