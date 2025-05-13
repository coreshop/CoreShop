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

namespace CoreShop\Bundle\OrderBundle\StateResolver;

use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;

final class OrderShippingStateResolver implements StateResolverInterface
{
    public function __construct(
        private StateMachineManager $stateMachineManager,
        private OrderShipmentRepositoryInterface $orderShipmentRepository,
        private ProcessableInterface $processable,
    ) {
    }

    public function resolve(OrderInterface $order): void
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

    private function countOrderShipmentsInState(OrderInterface $order, string $shipmentState): int
    {
        $shipments = $this->orderShipmentRepository->getDocuments($order);

        $items = 0;
        /** @var OrderShipmentInterface $shipment */
        foreach ($shipments as $shipment) {
            if ($shipment->getState() === $shipmentState) {
                ++$items;
            }
        }

        return $items;
    }

    private function allShipmentsInStateButOrderStateNotUpdated(
        OrderInterface $order,
        string $shipmentState,
        string $orderShippingState,
    ): bool {
        $shipmentInStateAmount = $this->countOrderShipmentsInState($order, $shipmentState);
        $shipmentAmount = count($this->orderShipmentRepository->getDocumentsNotInState($order, OrderShipmentStates::STATE_CANCELLED));

        return $shipmentAmount === $shipmentInStateAmount &&
            $orderShippingState !== $order->getShippingState() &&
            $this->processable->isFullyProcessed($order);
    }

    private function isPartiallyShippedButOrderStateNotUpdated(OrderInterface $order): bool
    {
        $shipmentInShippedStateAmount = $this->countOrderShipmentsInState($order, ShipmentStates::STATE_SHIPPED);

        return
            $shipmentInShippedStateAmount > 0 &&
            !$this->processable->isFullyProcessed($order) &&
            OrderShipmentStates::STATE_PARTIALLY_SHIPPED !== $order->getShippingState();
    }
}
