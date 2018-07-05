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
    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

    /**
     * @var OrderInvoiceRepositoryInterface
     */
    private $orderInvoiceRepository;

    /**
     * @var ProcessableInterface
     */
    private $processable;

    /**
     * @param StateMachineManager             $stateMachineManager
     * @param OrderInvoiceRepositoryInterface $orderInvoiceRepository
     * @param ProcessableInterface            $processable
     */
    public function __construct(
        StateMachineManager $stateMachineManager,
        OrderInvoiceRepositoryInterface $orderInvoiceRepository,
        ProcessableInterface $processable
    ) {
        $this->stateMachineManager = $stateMachineManager;
        $this->orderInvoiceRepository = $orderInvoiceRepository;
        $this->processable = $processable;
    }

    /**
     * @param OrderInterface $order
     *
     * @return mixed|void
     */
    public function resolve(OrderInterface $order)
    {
        if (OrderInvoiceStates::STATE_INVOICED === $order->getInvoiceState()) {
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

    /**
     * @param OrderInterface $order
     * @param string         $invoiceState
     *
     * @return int
     */
    private function countOrderInvoicesInState(OrderInterface $order, string $invoiceState): int
    {
        $invoices = $this->orderInvoiceRepository->getDocuments($order);

        $items = 0;
        /** @var OrderInvoiceInterface $invoice */
        foreach ($invoices as $invoice) {
            if ($invoice->getState() === $invoiceState) {
                ++$items;
            }
        }

        return $items;
    }

    /**
     * @param OrderInterface $order
     * @param string         $invoiceState
     * @param string         $orderInvoiceState
     *
     * @return bool
     */
    private function allInvoicesInStateButOrderStateNotUpdated(
        OrderInterface $order,
        string $invoiceState,
        string $orderInvoiceState
    ): bool {
        $invoiceInStateAmount = $this->countOrderInvoicesInState($order, $invoiceState);
        $invoiceAmount = count($this->orderInvoiceRepository->getDocuments($order));

        return $invoiceAmount === $invoiceInStateAmount &&
            $orderInvoiceState !== $order->getInvoiceState() &&
            $this->processable->isFullyProcessed($order);
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isPartiallyInvoicedButOrderStateNotUpdated(OrderInterface $order): bool
    {
        $invoiceInCompleteStateAmount = $this->countOrderInvoicesInState($order, InvoiceStates::STATE_COMPLETE);

        return
            $invoiceInCompleteStateAmount > 0 &&
            !$this->processable->isFullyProcessed($order) &&
            OrderInvoiceStates::STATE_PARTIALLY_INVOICED !== $order->getInvoiceState();
    }
}
