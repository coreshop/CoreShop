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
use Webmozart\Assert\Assert;

final class DeleteIndexListener
{
    private $workerServiceRegistry;

    public function __construct(ServiceRegistryInterface $workerServiceRegistry)
    {
        $this->workerServiceRegistry = $workerServiceRegistry;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function onIndexDeletePre(ResourceControllerEvent $event): void
    {
        $resource = $event->getSubject();

        Assert::isInstanceOf($resource, IndexInterface::class);

        $worker = $resource->getWorker();

        // do not throw an exception since the worker field could be empty!
        if (!$this->workerServiceRegistry->has($worker)) {
            return;
        }

        /**
         * @var WorkerInterface $worker
         */
        $worker = $this->workerServiceRegistry->get($worker);
        $worker->deleteIndexStructures($resource);
    }
}
