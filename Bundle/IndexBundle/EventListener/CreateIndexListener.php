<?php

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Intl\Exception\InvalidArgumentException;

final class CreateIndexListener
{
    /**
     * @var ServiceRegistryInterface
     */
    private $workerServiceRegistry;

    /**
     * @param ServiceRegistryInterface $workerServiceRegistry
     */
    public function __construct(ServiceRegistryInterface $workerServiceRegistry)
    {
        $this->workerServiceRegistry = $workerServiceRegistry;
    }

    /**
     * Prevent channel deletion if no more channels enabled.
     *
     * @param ResourceControllerEvent $event
     */
    public function onIndexSavePost(ResourceControllerEvent $event)
    {
        $resource = $event->getSubject();

        if (!$resource instanceof IndexInterface) {
            throw new UnexpectedTypeException(
                $resource,
                IndexInterface::class
            );
        }

        $worker = $resource->getWorker();

        if (!$this->workerServiceRegistry->has($worker)) {
             throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
        }

        /**
         * @var $worker WorkerInterface
         */
        $worker = $this->workerServiceRegistry->get($worker);
        $worker->createOrUpdateIndexStructures($resource);
    }
}
