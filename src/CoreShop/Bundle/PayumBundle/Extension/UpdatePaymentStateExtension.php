<?php

namespace CoreShop\Bundle\PayumBundle\Extension;

use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Request\Notify;

final class UpdatePaymentStateExtension implements ExtensionInterface
{
    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

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
    public function onPreExecute(Context $context)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onExecute(Context $context)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onPostExecute(Context $context)
    {
        if ($context->getException()) {
            return;
        }

        $previousStack = $context->getPrevious();
        $previousStackSize = count($previousStack);

        if ($previousStackSize > 1) {
            return;
        }

        if (1 === $previousStackSize) {
            $previousActionClassName = get_class($previousStack[0]->getAction());
            if (false === stripos($previousActionClassName, 'NotifyNullAction')) {
                return;
            }
        }

        /** @var Generic $request */
        $request = $context->getRequest();
        if (false === $request instanceof Generic) {
            return;
        }

        if (false === $request instanceof GetStatusInterface && false === $request instanceof Notify) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $context->getGateway()->execute($status = new GetStatus($payment));
        $value = $status->getValue();
        if ($payment->getState() !== $value && PaymentInterface::STATE_UNKNOWN !== $value) {
            $this->updatePaymentState($payment, $value);
        }
    }

    /**
     * @param PaymentInterface $payment
     * @param string           $nextState
     */
    private function updatePaymentState(PaymentInterface $payment, string $nextState)
    {
        $workflow = $this->stateMachineManager->get($payment, PaymentTransitions::IDENTIFIER);
        if (null !== $transition = $this->stateMachineManager->getTransitionToState($workflow, $payment, $nextState)) {
            $workflow->apply($payment, $transition);
        }
    }
}
