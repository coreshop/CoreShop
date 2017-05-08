<?php

namespace CoreShop\Component\Index\Service;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\Object\Concrete;
use Psr\Log\InvalidArgumentException;

final class IndexUpdaterService implements IndexUpdaterServiceInterface
{
    /**
     * @var RepositoryInterface
     */
    private $indexRepository;

    /**
     * @var ServiceRegistryInterface
     */
    private $workerServiceRegistry;

    /**
     * @param RepositoryInterface $indexRepository
     * @param ServiceRegistryInterface $workerServiceRegistry
     */
    public function __construct(RepositoryInterface $indexRepository, ServiceRegistryInterface $workerServiceRegistry)
    {
        $this->indexRepository = $indexRepository;
        $this->workerServiceRegistry = $workerServiceRegistry;
    }

    public function updateIndices($subject)
    {
        $indices = $this->indexRepository->findAll();

        foreach ($indices as $index) {
            if ($index instanceof IndexInterface) {
                if ($subject instanceof Concrete) {
                    if ($subject->getClass()->getName() === $index->getClass()) {
                        $worker = $index->getWorker();

                        if (!$this->workerServiceRegistry->has($worker)) {
                            throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
                        }

                        /**
                         * @var WorkerInterface
                         */
                        $worker = $this->workerServiceRegistry->get($worker);
                        $worker->updateIndex($index, $subject);
                    }
                }
            }
        }
    }
}