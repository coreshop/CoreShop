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

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;

final class CreateIndexListener
{
    public function __construct(
        private ServiceRegistryInterface $workerServiceRegistry,
    ) {
    }

    public function onIndexSavePost(ResourceControllerEvent $event): void
    {
        $resource = $event->getSubject();

        Assert::isInstanceOf($resource, IndexInterface::class);

        $worker = $resource->getWorker();

        if (!$this->workerServiceRegistry->has($worker)) {
            throw new \InvalidArgumentException(sprintf('%s Worker not found', $worker));
        }

        $worker = $this->workerServiceRegistry->get($worker);
        $worker->createOrUpdateIndexStructures($resource);
    }
}
