<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventDispatcher implements EventDispatcherInterface
{
    private $eventDispatcher;

    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
            $event
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPreEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.pre_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
            $event
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPostEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.post_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
            $event
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchInitializeEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request): void
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.initialize_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
            $event
        );
    }

    private function getEvent(ResourceInterface $resource, Request $request): ResourceControllerEvent
    {
        return new ResourceControllerEvent($resource, ['request' => $request]);
    }
}
