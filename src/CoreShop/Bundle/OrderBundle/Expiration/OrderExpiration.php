<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Expiration;

use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplier;
use CoreShop\Bundle\WorkflowBundle\History\HistoryLoggerInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Pimcore\Model\DataObject\Concrete;

final class OrderExpiration implements ProposalExpirationInterface
{
    private $orderRepository;
    private $stateMachineApplier;
    private $historyLogger;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StateMachineApplier $stateMachineApplier,
        HistoryLoggerInterface $historyLogger
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineApplier = $stateMachineApplier;
        $this->historyLogger = $historyLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function expire(int $days, array $params = []): void
    {
        if ($days <= 0) {
            return;
        }

        $orders = $this->orderRepository->findExpiredOrders($days);

        if (is_array($orders)) {
            foreach ($orders as $order) {
                $this->stateMachineApplier->apply(
                    $order,
                    OrderTransitions::IDENTIFIER,
                    OrderTransitions::TRANSITION_CANCEL
                );

                if (null !== $this->historyLogger && $order instanceof Concrete) {
                    $this->historyLogger->log(
                        $order,
                        'Automatic Expiration Order Cancellation'
                    );
                }
            }
        }
    }
}
