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

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private SymfonyEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function dispatch($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
        );
    }

    public function dispatchPreEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.pre_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
        );
    }

    public function dispatchPostEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.post_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
        );
    }

    public function dispatchInitializeEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.initialize_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
        );
    }

    private function getEvent(ResourceInterface $resource, Request $request): ResourceControllerEvent
    {
        return new ResourceControllerEvent($resource, ['request' => $request]);
    }
}
