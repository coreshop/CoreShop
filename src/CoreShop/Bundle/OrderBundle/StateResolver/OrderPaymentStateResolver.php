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
use CoreShop\Component\Order\OrderPaymentTransitions;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderPaymentStateResolver implements StateResolverInterface
{
    public function __construct(
        private StateMachineManager $stateMachineManager,
        private PaymentRepositoryInterface $paymentRepository,
    ) {
    }

    public function resolve(OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderPaymentTransitions::IDENTIFIER);
        $targetTransition = $this->getTargetTransition($order);

        if (null !== $targetTransition) {
            $this->applyTransition($workflow, $order, $targetTransition);
        }
    }

    private function applyTransition(WorkflowInterface $workflow, OrderInterface $subject, string $transition): void
    {
        if ($workflow->can($subject, $transition)) {
            $workflow->apply($subject, $transition);
        }
    }

    private function getTargetTransition(OrderInterface $order): ?string
    {
        $refundedPaymentTotal = 0;
        $refundedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_REFUNDED);

        /** @var PaymentInterface $payment */
        foreach ($refundedPayments as $payment) {
            $refundedPaymentTotal += $payment->getTotalAmount();
        }

        if (count($refundedPayments) > 0 && $refundedPaymentTotal >= $order->getPaymentTotal()) {
            return OrderPaymentTransitions::TRANSITION_REFUND;
        }

        if ($refundedPaymentTotal < $order->getPaymentTotal() && 0 < $refundedPaymentTotal) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND;
        }

        $completedPaymentTotal = 0;
        $completedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_COMPLETED);

        foreach ($completedPayments as $payment) {
            $completedPaymentTotal += $payment->getTotalAmount();
        }

        $payments = $this->paymentRepository->findForPayable($order);
        if ((count($completedPayments) > 0 && $completedPaymentTotal >= $order->getPaymentTotal()) || count($payments) === 0) {
            return OrderPaymentTransitions::TRANSITION_PAY;
        }

        if ($completedPaymentTotal < $order->getPaymentTotal() && $completedPaymentTotal > 0) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY;
        }

        $authorizedPaymentTotal = 0;
        $authorizedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_AUTHORIZED);

        foreach ($authorizedPayments as $payment) {
            $authorizedPaymentTotal += $payment->getTotalAmount();
        }

        if (count($authorizedPayments) > 0 && $authorizedPaymentTotal >= $order->getPaymentTotal()) {
            return OrderPaymentTransitions::TRANSITION_AUTHORIZE;
        }

        if ($authorizedPaymentTotal < $order->getPaymentTotal() && $authorizedPaymentTotal > 0) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_AUTHORIZE;
        }

        // Processing payments
        $processingPaymentTotal = 0;
        $processingPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_PROCESSING);

        foreach ($processingPayments as $payment) {
            $processingPaymentTotal += $payment->getTotalAmount();
        }

        if (count($processingPayments) > 0 && $processingPaymentTotal >= $order->getPaymentTotal()) {
            return OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT;
        }

        return null;
    }

    /**
     * @return PaymentInterface[]
     */
    private function getPaymentsWithState(OrderInterface $order, string $state): array
    {
        $payments = $this->paymentRepository->findForPayable($order);
        $filteredPayments = [];
        foreach ($payments as $payment) {
            if ($payment->getState() === $state) {
                $filteredPayments[] = $payment;
            }
        }

        return $filteredPayments;
    }
}
