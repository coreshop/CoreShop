<?php

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Event\Model\ObjectEvent;
use Pimcore\Model\Object\Concrete;
use Symfony\Component\Intl\Exception\InvalidArgumentException;

class IndexObjectListener
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
    public function __construct(
        RepositoryInterface $indexRepository,
        ServiceRegistryInterface $workerServiceRegistry
    )
    {
        $this->indexRepository = $indexRepository;
        $this->workerServiceRegistry = $workerServiceRegistry;
    }


    public function onPostUpdate (ElementEventInterface $e) {

         if ($e instanceof ObjectEvent) {
            $indices = $this->indexRepository->findAll();
            $object = $e->getObject();

            foreach ($indices as $index) {
                if ($index instanceof IndexInterface) {
                    if ($object instanceof Concrete) {
                        if ($object->getClass()->getName() === $index->getClass()) {
                            $worker = $index->getWorker();

                            if (!$this->workerServiceRegistry->has($worker)) {
                                 throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
                            }

                            /**
                             * @var $worker WorkerInterface
                             */
                            $worker = $this->workerServiceRegistry->get($worker);
                            $worker->updateIndex($index, $object);
                        }
                    }
                }
            }
        }
    }
}