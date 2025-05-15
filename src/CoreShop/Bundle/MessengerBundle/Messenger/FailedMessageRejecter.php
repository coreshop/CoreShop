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
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final class FailedMessageRejecter implements FailedMessageRejecterInterface
{
    public function __construct(
        private FailureReceiversRepositoryInterface $failureReceivers,
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
    }
}
