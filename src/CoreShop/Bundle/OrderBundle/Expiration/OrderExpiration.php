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

namespace CoreShop\Bundle\OrderBundle\Expiration;

use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplier;

final class OrderExpiration implements ProposalExpirationInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var StateMachineApplier
     */
    private $stateMachineApplier;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param StateMachineApplier      $stateMachineApplier
     */
    public function __construct(OrderRepositoryInterface $orderRepository, StateMachineApplier $stateMachineApplier)
    {
        $this->orderRepository = $orderRepository;
        $this->stateMachineApplier = $stateMachineApplier;
    }

    /**
     * {@inheritdoc}
     */
    public function expire($days, $params = [])
    {
        if ($days <= 0) {
            return;
        }

        $orders = $this->orderRepository->findExpiredOrders($days);

        if (is_array($orders)) {
            foreach ($orders as $order) {
                $this->stateMachineApplier->apply($order, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CANCEL);
            }
        }
    }
}
