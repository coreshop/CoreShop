<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var SymfonyEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param SymfonyEventDispatcherInterface $eventDispatcher
     */
    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request)
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
    public function dispatchPreEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request)
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
    public function dispatchPostEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request)
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
    public function dispatchInitializeEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request)
    {
        $event = $this->getEvent($resource, $request);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.initialize_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
            $event
        );
    }

    /**
     * @param ResourceInterface $resource
     * @param Request           $request
     *
     * @return ResourceControllerEvent
     */
    private function getEvent(ResourceInterface $resource, Request $request)
    {
        return new ResourceControllerEvent($resource, ['request' => $request]);
    }
}
