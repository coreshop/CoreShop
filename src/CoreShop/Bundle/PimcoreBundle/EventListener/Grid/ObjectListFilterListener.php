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

namespace CoreShop\Bundle\PimcoreBundle\EventListener\Grid;

use CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ObjectListFilterListener
{
    private ServiceRegistryInterface $filterServiceRegistry;

    public function __construct(ServiceRegistryInterface $filterServiceRegistry)
    {
        $this->filterServiceRegistry = $filterServiceRegistry;
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
