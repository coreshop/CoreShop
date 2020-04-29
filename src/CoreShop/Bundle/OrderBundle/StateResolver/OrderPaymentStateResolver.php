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
use CoreShop\Component\Order\OrderPaymentTransitions;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use Symfony\Component\Workflow\Workflow;

final class OrderPaymentStateResolver implements StateResolverInterface
{
    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

    /**
     * @var PaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @param StateMachineManager        $stateMachineManager
     * @param PaymentRepositoryInterface $paymentRepository
     */
    public function __construct(StateMachineManager $stateMachineManager, PaymentRepositoryInterface $paymentRepository)
    {
        $this->stateMachineManager = $stateMachineManager;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $workflow = $this->stateMachineManager->get($order, OrderPaymentTransitions::IDENTIFIER);
        $targetTransition = $this->getTargetTransition($order);

        if (null !== $targetTransition) {
            $this->applyTransition($workflow, $order, $targetTransition);
        }
    }

    /**
     * @param Workflow       $workflow
     * @param OrderInterface $subject
     * @param string         $transition
     */
    private function applyTransition(Workflow $workflow, $subject, string $transition)
    {
        if ($workflow->can($subject, $transition)) {
            $workflow->apply($subject, $transition);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return string|null
     */
    private function getTargetTransition(OrderInterface $order)
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

        return null;
    }

    /**
     * @param OrderInterface $order
     * @param string         $state
     *
     * @return PaymentInterface[]
     */
    private function getPaymentsWithState(OrderInterface $order, string $state)
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
