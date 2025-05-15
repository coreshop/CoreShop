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

use CoreShop\Bundle\MessengerBundle\Exception\ReceiverDoesNotExistException;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

class ReceiversRepository implements ReceiversRepositoryInterface
{
    public function __construct(
        private ServiceProviderInterface $receiverLocator,
        private array $receiverNames,
    ) {
    }

    /**
     * @return ReceiverInterface[]
     */
    public function getReceiversMapping(): array
    {
        $receivers = [];
        foreach ($this->receiverNames as $receiverName) {
            $receivers[$receiverName] = $this->getReceiver($receiverName);
        }

        return $receivers;
    }

    /**
     * @return ReceiverInterface[]
     */
    public function getListableReceiversMapping(): array
    {
        $receivers = [];
        foreach ($this->receiverNames as $receiverName) {
            $receiver = $this->getReceiver($receiverName);

            if (!$receiver instanceof ListableReceiverInterface) {
                continue;
            }

            $receivers[$receiverName] = $receiver;
        }

        return $receivers;
    }

    public function getReceiver(string $receiverName): ReceiverInterface
    {
        if (!\in_array($receiverName, $this->receiverNames, true) || !$this->receiverLocator->has($receiverName)) {
            throw new ReceiverDoesNotExistException($receiverName, $this->receiverNames);
        }

        return $this->receiverLocator->get($receiverName);
    }
}
