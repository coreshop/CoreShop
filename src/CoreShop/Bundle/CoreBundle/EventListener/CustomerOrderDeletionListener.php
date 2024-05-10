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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Pimcore\Bundle\AdminBundle\Event\Model\DataObjectDeleteInfoEvent;
use Pimcore\Event\Model\DataObjectEvent;

final class CustomerOrderDeletionListener
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function checkCustomerDeletionAllowed(DataObjectDeleteInfoEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof CustomerInterface) {
            return;
        }

        $hasOrders = $this->orderRepository->hasCustomerOrders($object);

        if ($hasOrders) {
            $event->setDeletionAllowed(false);
            $event->setReason('Cannot delete a customer with orders');
        }
    }

    public function checkCustomerOrdersBeforeDeletion(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof CustomerInterface) {
            return;
        }

        $hasOrders = $this->orderRepository->hasCustomerOrders($object);

        if ($hasOrders) {
            throw new \InvalidArgumentException('Cannot delete a customer with orders');
        }
    }
}
