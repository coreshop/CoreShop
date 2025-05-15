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

namespace CoreShop\Bundle\MessengerBundle\EventListener;

use CoreShop\Bundle\MessengerBundle\Stamp\PimcoreObjectStamp;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

class FailureListener implements EventSubscriberInterface
{
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageFailedEvent::class => ['onMessageFailed', 110],
        ];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        if ($event->willRetry()) {
            return;
        }

        $envelope = $event->getEnvelope();

        /**
         * @var BusNameStamp|null $busNameStamp
         */
        $busNameStamp = $envelope->last(BusNameStamp::class);

        if (null !== $busNameStamp && $busNameStamp->getBusName() !== 'coreshop.bus') {
            return;
        }

        $errorStamp = $envelope->last(ErrorDetailsStamp::class);

        if ($errorStamp instanceof ErrorDetailsStamp) {
            /** @var RedeliveryStamp|null $lastRedeliveryStamp */
            $lastRedeliveryStamp = $envelope->last(RedeliveryStamp::class);
            /** @var PimcoreObjectStamp|null $pimcoreObjectStamp */
            $pimcoreObjectStamp = $envelope->last(PimcoreObjectStamp::class);

            /**
             * @var TransportMessageIdStamp|null $messageIdStamp
             */
            $messageIdStamp = $envelope->last(TransportMessageIdStamp::class);
            $messageId = $messageIdStamp?->getId();
            $messageClass = \get_class($envelope->getMessage());
            $lastRedelivery = null === $lastRedeliveryStamp ? '' : $lastRedeliveryStamp->getRedeliveredAt()->format(
                'Y-m-d H:i:s',
            );
            $errorMessage = $errorStamp->getExceptionMessage();
            $relatedObject = null === $pimcoreObjectStamp ? '' : $pimcoreObjectStamp->getObjectId();

            $message = 'Message with ID "%s" of Class "%s" failed. Error was: "%s"';
            $messageParams = [
                $messageId,
                $messageClass,
                $errorMessage,
            ];

            if ($lastRedelivery) {
                $message = 'Message with ID "%s" of Class "%s" failed. Redelivery tried at "%s". Error was: "%s"';
                array_splice($messageParams, 2, 0, $lastRedelivery);
            }

            $this->logger->alert(sprintf($message, ...$messageParams), [
                'messageId' => $messageId,
                'messageClass' => $messageClass,
                'lastRedelivery' => $lastRedelivery,
                'error' => $errorMessage,
                'relatedObject' => $relatedObject,
                'source' => $errorStamp->getFlattenException()?->getTraceAsString(),
            ]);
        }
    }
}
