<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener\Order\Grid;

use CoreShop\Component\Core\OrderList\OrderListFilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ObjectListFilterListener
{
    /**
     * @var ServiceRegistryInterface
     */
    private $filterServiceRegistry;

    /**
     * @param ServiceRegistryInterface $filterServiceRegistry
     */
    public function __construct(ServiceRegistryInterface $filterServiceRegistry)
    {
        $this->filterServiceRegistry = $filterServiceRegistry;
    }

    /**
     * @param GenericEvent $event
     */
    public function checkObjectList(GenericEvent $event)
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

        /** @var OrderListFilterInterface $filterService */
        $filterService = $this->filterServiceRegistry->get($filter);
        $data = $filterService->filter($list, $context);

        if ($data instanceof DataObject\Listing) {
            $event->setArgument('list', $data);
        }
    }
}