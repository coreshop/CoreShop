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

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Intl\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;

final class CreateIndexListener
{
    public function __construct(private ServiceRegistryInterface $workerServiceRegistry)
    {
    }

    public function onIndexSavePost(ResourceControllerEvent $event): void
    {
        $resource = $event->getSubject();

        Assert::isInstanceOf($resource, IndexInterface::class);

        $worker = $resource->getWorker();

        if (!$this->workerServiceRegistry->has($worker)) {
            throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
        }

        $worker = $this->workerServiceRegistry->get($worker);
        $worker->createOrUpdateIndexStructures($resource);
    }
}
