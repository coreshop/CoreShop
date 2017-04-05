<?php

namespace CoreShop\Bundle\IndexBundle;

use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Model\Index;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Intl\Exception\InvalidArgumentException;

class ListingFactory implements ListingFactoryInterface {
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
     * {@inheritdoc}
     */
    public function createList(IndexInterface $index)
    {
        $worker = $index->getWorker();

        if (!$this->workerServiceRegistry->has($worker)) {
             throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
        }

        /**
         * @var $worker WorkerInterface
         */
        $worker = $this->workerServiceRegistry->get($worker);

        return $worker->getList($index, $worker);
    }
}