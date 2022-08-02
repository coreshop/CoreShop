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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Pimcore\Event\Model\DataObjectDeleteInfoEvent;
use Pimcore\Event\Model\DataObjectEvent;

final class CustomerOrderDeletionListener
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
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
            throw new \InvalidArgumentException(sprintf('Cannot delete a customer with orders'));
        }
    }
}
