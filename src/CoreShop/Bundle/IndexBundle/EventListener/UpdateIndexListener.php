<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Intl\Exception\InvalidArgumentException;

final class UpdateIndexListener
{
    private $workerServiceRegistry;

    public function __construct(ServiceRegistryInterface $workerServiceRegistry)
    {
        $this->workerServiceRegistry = $workerServiceRegistry;
    }

    public function onPreUpdate(IndexInterface $index, PreUpdateEventArgs $event)
    {
        if (!$event->hasChangedField('name')) {
            return;
        }

        $newName = $event->getNewValue('name');
        $oldName = $event->getOldValue('name');

        $workerType = $index->getWorker();

        if (!$this->workerServiceRegistry->has($workerType)) {
            throw new InvalidArgumentException(sprintf('%s Worker not found', $workerType));
        }

        /**
         * @var WorkerInterface $worker
         */
        $worker = $this->workerServiceRegistry->get($workerType);

        //BC Safe, remove in CoreShop 3.0 and add renamedIndexStructures to interface of Workers
        if (method_exists($worker, 'renameIndexStructures')) {
            $worker->renameIndexStructures($index, $oldName, $newName);
        }
    }
}
