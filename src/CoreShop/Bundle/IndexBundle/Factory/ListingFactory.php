<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Factory;

use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Intl\Exception\InvalidArgumentException;

class ListingFactory implements ListingFactoryInterface
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
     * {@inheritdoc}
     */
    public function createList(IndexInterface $index)
    {
        $worker = $index->getWorker();

        if (!$this->workerServiceRegistry->has($worker)) {
            throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
        }

        /**
         * @var WorkerInterface
         */
        $worker = $this->workerServiceRegistry->get($worker);

        return $worker->getList($index);
    }
}
