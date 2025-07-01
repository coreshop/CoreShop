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

namespace CoreShop\Bundle\MessengerBundle\Event;

use CoreShop\Bundle\MessengerBundle\Messenger\MessageDetails;
use Symfony\Component\Messenger\Envelope;
use Symfony\Contracts\EventDispatcher\Event;

final class MessageDetailsEvent extends Event
{
    public function __construct(
        private string $receiverName,
        private Envelope $envelope,
        private MessageDetails $messageDetails,
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

    public function getMessageDetails(): MessageDetails
    {
        return $this->messageDetails;
    }
}
