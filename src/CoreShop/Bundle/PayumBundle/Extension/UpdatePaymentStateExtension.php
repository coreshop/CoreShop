<?php

namespace CoreShop\Bundle\PayumBundle\Extension;

use CoreShop\Bundle\CoreBundle\StateMachine\PaymentTransitions;
use CoreShop\Bundle\CoreBundle\StateMachine\StateMachineManager;
use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use CoreShop\Component\Payment\Model\PaymentInterface;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param StateMachineManager    $stateMachineManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(StateMachineManager $stateMachineManager, EntityManagerInterface $entityManager)
    {
        $this->stateMachineManager = $stateMachineManager;
        $this->entityManager = $entityManager;
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
        $previousStack = $context->getPrevious();
        $previousStackSize = count($previousStack);

        if ($previousStackSize > 1) {
            return;
        }

        if ($previousStackSize === 1) {
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
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
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

        //$cart = $this->cartRepository->findCartByOrder($order);
        //if ($cart instanceof CartInterface) {
            //$cart->setOrder(null);
            //$cart->save();
        //}
    }
}
