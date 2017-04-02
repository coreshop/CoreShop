<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatch($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPreEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPostEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchInitializeEvent(
        $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    );
}
