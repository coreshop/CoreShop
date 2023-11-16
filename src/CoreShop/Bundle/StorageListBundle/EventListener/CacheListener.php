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

namespace CoreShop\Bundle\StorageListBundle\EventListener;

use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CacheListener implements EventSubscriberInterface
{
    public function __construct(
        private PimcoreRepositoryInterface $storageListRepository,
        private PimcoreRepositoryInterface $storageListItemRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        Cache::addIgnoredTagOnSave(sprintf('object_%s', $this->storageListRepository->getClassId()));
        Cache::addIgnoredTagOnSave(sprintf('object_%s', $this->storageListItemRepository->getClassId()));
    }
}
