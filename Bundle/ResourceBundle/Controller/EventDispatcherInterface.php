<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;

interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request $request
     */
    public function dispatch($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);

    /**
     * @param string $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request $request
     */
    public function dispatchPreEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);

    /**
     * @param string $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request $request
     */
    public function dispatchPostEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);

    /**
     * @param string $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request $request
     */
    public function dispatchInitializeEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);
}
