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

namespace CoreShop\Bundle\OrderBundle\StateResolver;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;

final class OrderShippingStateResolver implements StateResolverInterface
{
    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

    /**
     * @var OrderShipmentRepositoryInterface
     */
    private $orderShipmentRepository;

    /**
     * @var ProcessableInterface
     */
    private $processable;

    /**
     * @param StateMachineManager              $stateMachineManager
     * @param OrderShipmentRepositoryInterface $orderShipmentRepository
     * @param ProcessableInterface             $processable
     */
    public function __construct(
        StateMachineManager $stateMachineManager,
        OrderShipmentRepositoryInterface $orderShipmentRepository,
        ProcessableInterface $processable
    ) {
        $this->stateMachineManager = $stateMachineManager;
        $this->orderShipmentRepository = $orderShipmentRepository;
        $this->processable = $processable;
    }

    /**
     * @param OrderInterface $order
     *
     * @return mixed|void
     */
    public function resolve(OrderInterface $order)
    {
        if ($order->getShippingState() === OrderShipmentStates::STATE_SHIPPED) {
            return;
        }

        $workflow = $this->stateMachineManager->get($order, OrderShipmentTransitions::IDENTIFIER);

        if ($this->allShipmentsInStateButOrderStateNotUpdated($order, ShipmentStates::STATE_SHIPPED, OrderShipmentStates::STATE_SHIPPED)) {
            $workflow->apply($order, OrderShipmentTransitions::TRANSITION_SHIP);
        }

        if ($this->isPartiallyShippedButOrderStateNotUpdated($order)) {
            $workflow->apply($order, OrderShipmentTransitions::TRANSITION_PARTIALLY_SHIP);
        }
    }

    /**
     * @param OrderInterface $order
     * @param string         $shipmentState
     *
     * @return int
     */
    private function countOrderShipmentsInState(OrderInterface $order, string $shipmentState): int
    {
        $shipments = $this->orderShipmentRepository->getDocuments($order);

        $items = 0;
        /** @var OrderShipmentInterface $shipment */
        foreach ($shipments as $shipment) {
            if ($shipment->getState() === $shipmentState) {
                $items++;
            }
        }

        return $items;
    }

    /**
     * @param OrderInterface $order
     * @param string         $shipmentState
     * @param string         $orderShippingState
     *
     * @return bool
     */
    private function allShipmentsInStateButOrderStateNotUpdated(
        OrderInterface $order,
        string $shipmentState,
        string $orderShippingState
    ): bool {
        $shipmentInStateAmount = $this->countOrderShipmentsInState($order, $shipmentState);
        $shipmentAmount = count($this->orderShipmentRepository->getDocumentsNotInState($order, OrderShipmentStates::STATE_CANCELLED));

        return $shipmentAmount === $shipmentInStateAmount &&
            $orderShippingState !== $order->getShippingState() &&
            $this->processable->isFullyProcessed($order);
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isPartiallyShippedButOrderStateNotUpdated(OrderInterface $order): bool
    {
        $shipmentInShippedStateAmount = $this->countOrderShipmentsInState($order, ShipmentStates::STATE_SHIPPED);

        return
            $shipmentInShippedStateAmount > 0 &&
            !$this->processable->isFullyProcessed($order) &&
            OrderShipmentStates::STATE_PARTIALLY_SHIPPED !== $order->getShippingState();
    }
}
