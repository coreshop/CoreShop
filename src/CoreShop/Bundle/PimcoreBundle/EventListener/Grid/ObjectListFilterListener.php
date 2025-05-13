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

namespace CoreShop\Bundle\PimcoreBundle\EventListener\Grid;

use CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ObjectListFilterListener
{
    public function __construct(
        private ServiceRegistryInterface $filterServiceRegistry,
    ) {
    }

    public function checkObjectList(GenericEvent $event): void
    {
        $list = $event->getArgument('list');
        $context = $event->getArgument('context');

        if (!isset($context['coreshop_filter'])) {
            return;
        }

        $filter = $context['coreshop_filter'];
        if (!$this->filterServiceRegistry->has($filter)) {
            return;
        }

        /** @var GridFilterInterface $filterService */
        $filterService = $this->filterServiceRegistry->get($filter);
        $data = $filterService->filter($list, $context);

        $event->setArgument('list', $data);
    }
}
