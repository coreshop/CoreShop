<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Pimcore\Event\Model\DataObjectEvent;

final class CustomerOrderDeletionListener
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function checkCustomerOrdersBeforeDeletion(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof CustomerInterface) {
            return;
        }

        if ($this->orderRepository->hasCustomerOrders($object)) {
            throw new \InvalidArgumentException('Cannot delete a customer with orders');
        }
    }
}
