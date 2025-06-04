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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

final class UpdateIndexListener
{
    public function __construct(
        private ServiceRegistryInterface $workerServiceRegistry,
    ) {
    }

    public function onPreUpdate(IndexInterface $index, PreUpdateEventArgs $event): void
    {
        if (!$event->hasChangedField('name')) {
            return;
        }

        $newName = $event->getNewValue('name');
        $oldName = $event->getOldValue('name');

        $workerType = $index->getWorker();

        if (!$this->workerServiceRegistry->has($workerType)) {
            throw new \InvalidArgumentException(sprintf('%s Worker not found', $workerType));
        }

        /**
         * @var WorkerInterface $worker
         */
        $worker = $this->workerServiceRegistry->get($workerType);

        $worker->renameIndexStructures($index, $oldName, $newName);
    }
}
