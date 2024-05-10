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

namespace CoreShop\Bundle\OrderBundle\Expiration;

use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplier;
use CoreShop\Bundle\WorkflowBundle\History\HistoryLoggerInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\StorageList\Expiration\StorageListExpirationInterface;
use Pimcore\Model\DataObject\Concrete;

final class OrderExpiration implements StorageListExpirationInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private StateMachineApplier $stateMachineApplier,
        private HistoryLoggerInterface $historyLogger,
    ) {
    }

    public function expire(int $days, array $params = []): void
    {
        if ($days <= 0) {
            return;
        }

        $orders = $this->orderRepository->findExpiredOrders($days);

        foreach ($orders as $order) {
            $this->stateMachineApplier->apply(
                $order,
                OrderTransitions::IDENTIFIER,
                OrderTransitions::TRANSITION_CANCEL,
            );

            if ($order instanceof Concrete) {
                $this->historyLogger->log(
                    $order,
                    'Automatic Expiration Order Cancellation',
                );
            }
        }
    }
}
