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

namespace CoreShop\Bundle\CoreBundle\Order\StateResolver;

use CoreShop\Component\Core\OrderPaymentTransitions;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Workflow\StateMachineManager;
use Symfony\Component\Workflow\Workflow;

final class OrderPaymentStateResolver implements StateResolverInterface
{
    /**
     * @var StateMachineManager
     */
    protected $stateMachineManager;

    /**
     * @param StateMachineManager $stateMachineManager
     */
    public function __construct(StateMachineManager $stateMachineManager)
    {
        $this->stateMachineManager = $stateMachineManager;
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
     * @param Workflow $workflow
     * @param          $subject
     * @param string   $transition
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

        if (count($refundedPayments) > 0 && $refundedPaymentTotal >= $order->getTotal()) {
            return OrderPaymentTransitions::TRANSITION_REFUND;
        }

        if ($refundedPaymentTotal < $order->getTotal() && 0 < $refundedPaymentTotal) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND;
        }

        $completedPaymentTotal = 0;
        $completedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_COMPLETED);

        foreach ($completedPayments as $payment) {
            $completedPaymentTotal += $payment->getTotalAmount();
        }

        if ((count($completedPayments) > 0 && $completedPaymentTotal >= $order->getTotal()) || count($order->getPayments()) === 0) {
            return OrderPaymentTransitions::TRANSITION_PAY;
        }

        if ($completedPaymentTotal < $order->getTotal() && $completedPaymentTotal > 0) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY;
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
        $filteredPayments = [];
        foreach ($order->getPayments() as $payment) {
            if ($payment->getState() === $state) {
                $filteredPayments[] = $payment;
            }
        }
        return $filteredPayments;
    }
}
