<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\StateResolver;

use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;

final class OrderInvoiceStateResolver implements StateResolverInterface
{
    private StateMachineManager $stateMachineManager;
    private OrderInvoiceRepositoryInterface $orderInvoiceRepository;
    private ProcessableInterface $processable;

    public function __construct(
        StateMachineManager $stateMachineManager,
        OrderInvoiceRepositoryInterface $orderInvoiceRepository,
        ProcessableInterface $processable
    ) {
        $this->stateMachineManager = $stateMachineManager;
        $this->orderInvoiceRepository = $orderInvoiceRepository;
        $this->processable = $processable;
    }

    public function resolve(OrderInterface $order): void
    {
        if ($order->getInvoiceState() === OrderInvoiceStates::STATE_INVOICED) {
            return;
        }

        $workflow = $this->stateMachineManager->get($order, OrderInvoiceTransitions::IDENTIFIER);

        if ($this->allInvoicesInStateButOrderStateNotUpdated($order, InvoiceStates::STATE_COMPLETE, OrderInvoiceStates::STATE_INVOICED)) {
            $workflow->apply($order, OrderInvoiceTransitions::TRANSITION_INVOICE);
        }

        if ($this->isPartiallyInvoicedButOrderStateNotUpdated($order)) {
            $workflow->apply($order, OrderInvoiceTransitions::TRANSITION_PARTIALLY_INVOICE);
        }
    }

    private function countOrderInvoicesInState(OrderInterface $order, string $invoiceState): int
    {
        $invoices = $this->orderInvoiceRepository->getDocuments($order);

        $items = 0;
        /** @var OrderInvoiceInterface $invoice */
        foreach ($invoices as $invoice) {
            if ($invoice->getState() === $invoiceState) {
                $items++;
            }
        }

        return $items;
    }

    private function allInvoicesInStateButOrderStateNotUpdated(
        OrderInterface $order,
        string $invoiceState,
        string $orderInvoiceState
    ): bool {
        $invoiceInStateAmount = $this->countOrderInvoicesInState($order, $invoiceState);
        $invoiceAmount = count($this->orderInvoiceRepository->getDocumentsNotInState($order, OrderInvoiceStates::STATE_CANCELLED));

        return $invoiceAmount === $invoiceInStateAmount &&
            $orderInvoiceState !== $order->getInvoiceState() &&
            $this->processable->isFullyProcessed($order);
    }

    private function isPartiallyInvoicedButOrderStateNotUpdated(OrderInterface $order): bool
    {
        $invoiceInCompleteStateAmount = $this->countOrderInvoicesInState($order, InvoiceStates::STATE_COMPLETE);

        return
            $invoiceInCompleteStateAmount > 0 &&
            !$this->processable->isFullyProcessed($order) &&
            OrderInvoiceStates::STATE_PARTIALLY_INVOICED !== $order->getInvoiceState();
    }
}
