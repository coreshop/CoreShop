<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Intl\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;

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

        Assert::isInstanceOf($resource, IndexInterface::class);

        $worker = $resource->getWorker();

        if (!$this->workerServiceRegistry->has($worker)) {
            throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
        }

        /**
         * @var WorkerInterface
         */
        $worker = $this->workerServiceRegistry->get($worker);
        $worker->createOrUpdateIndexStructures($resource);
    }
}
