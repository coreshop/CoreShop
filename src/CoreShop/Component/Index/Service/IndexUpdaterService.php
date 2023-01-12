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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Index\Service;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerDeleteableByIdInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\InvalidArgumentException;

final class IndexUpdaterService implements IndexUpdaterServiceInterface
{
    public function __construct(
        private RepositoryInterface $indexRepository,
        private ServiceRegistryInterface $workerServiceRegistry,
    ) {
    }

    public function updateIndices(IndexableInterface $subject, bool $isVersionChange = false): void
    {
        $this->operationOnIndex($subject, 'update', $isVersionChange);
    }

    public function removeIndices(IndexableInterface $subject): void
    {
        $this->operationOnIndex($subject, 'remove');
    }

    public function removeFromIndicesById(string $className, int $id): void
    {
        $indices = $this->indexRepository->findAll();

        foreach ($indices as $index) {
            if (!$index instanceof IndexInterface) {
                continue;
            }

            if ($className !== $index->getClass()) {
                continue;
            }

            $workerName = $index->getWorker();

            if (!$this->workerServiceRegistry->has($workerName)) {
                throw new InvalidArgumentException(sprintf('%s Worker not found', $workerName));
            }

            /**
             * @var WorkerInterface $worker
             */
            $worker = $this->workerServiceRegistry->get($workerName);

            if ($worker instanceof WorkerDeleteableByIdInterface) {
                $worker->deleteFromIndexById($index, $id);
            }
        }
    }

    private function operationOnIndex(IndexableInterface $subject, string $operation = 'update', bool $isVersionChange = false): void
    {
        $indices = $this->indexRepository->findAll();

        foreach ($indices as $index) {
            if (!$index instanceof IndexInterface) {
                continue;
            }

            if (!$this->isEligible($index, $subject)) {
                continue;
            }

            //Don't store version changes into the index!
            if ($isVersionChange && !$index->getIndexLastVersion()) {
                continue;
            }

            $workerName = $index->getWorker();

            if (!$this->workerServiceRegistry->has($workerName)) {
                throw new InvalidArgumentException(sprintf('%s Worker not found', $workerName));
            }

            /**
             * @var WorkerInterface $worker
             */
            $worker = $this->workerServiceRegistry->get($workerName);

            if ($operation === 'update') {
                $worker->updateIndex($index, $subject);
            } else {
                $worker->deleteFromIndex($index, $subject);
            }
        }
    }

    private function isEligible(IndexInterface $index, IndexableInterface $subject): bool
    {
        if (!$subject instanceof Concrete) {
            return false;
        }

        if ($subject->getClass()->getName() !== $index->getClass()) {
            return false;
        }

        return true;
    }
}
