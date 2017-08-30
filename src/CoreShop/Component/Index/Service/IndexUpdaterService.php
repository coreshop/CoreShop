<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Index\Service;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\Concrete;
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
     * @param RepositoryInterface      $indexRepository
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
