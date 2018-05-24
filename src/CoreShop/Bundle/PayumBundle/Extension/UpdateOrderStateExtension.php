<?php

namespace CoreShop\Bundle\PayumBundle\Extension;

use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Generic;
use Payum\Core\Request\Notify;

final class UpdateOrderStateExtension implements ExtensionInterface
{
    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

    /**
     * @var PimcoreRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param StateMachineManager $stateMachineManager
     * @param PimcoreRepositoryInterface $orderRepository
     */
    public function __construct(StateMachineManager $stateMachineManager, PimcoreRepositoryInterface $orderRepository)
    {
        $this->stateMachineManager = $stateMachineManager;
        $this->orderRepository = $orderRepository;
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

        if (false === $request instanceof Notify) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $context->getGateway()->execute($status = new GetStatus($payment));
        $value = $status->getValue();
        if (PaymentInterface::STATE_UNKNOWN !== $value) {
            $order = $this->orderRepository->find($payment->getOrderId());
            if ($order instanceof OrderInterface) {
                $this->confirmOrderState($order);
            }
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function confirmOrderState(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineManager->get($order, 'coreshop_order');
        if ($stateMachine->can($order, OrderTransitions::TRANSITION_CONFIRM)) {
            $stateMachine->apply($order, OrderTransitions::TRANSITION_CONFIRM);
        }
    }
}
