<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

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
    public function dispatch($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();
        $event = $this->getEvent($resource);

        $this->eventDispatcher->dispatch(sprintf('%s.%s.%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPreEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();
        $event = $this->getEvent($resource);

        $this->eventDispatcher->dispatch(sprintf('%s.%s.pre_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPostEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();
        $event = $this->getEvent($resource);

        $this->eventDispatcher->dispatch(sprintf('%s.%s.post_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchInitializeEvent(
        $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ) {
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();
        $event = $this->getEvent($resource);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.initialize_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName),
            $event
        );

        return $event;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    private function getEvent(ResourceInterface $resource)
    {
        return new ResourceControllerEvent($resource);
    }
}
