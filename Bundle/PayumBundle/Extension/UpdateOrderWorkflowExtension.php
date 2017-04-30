<?php

namespace CoreShop\Bundle\PayumBundle\Extension;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Request\Notify;
use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use CoreShop\Component\Payment\Model\PaymentInterface;

final class UpdateOrderWorkflowExtension implements ExtensionInterface
{
    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var WorkflowManagerInterface
     */
    private $orderWorkflowManager;

    /**
     * @param PimcoreRepositoryInterface $orderRepository
     * @param WorkflowManagerInterface $orderWorkflowManager
     */
    public function __construct(PimcoreRepositoryInterface $orderRepository, WorkflowManagerInterface $orderWorkflowManager)
    {
        $this->orderRepository = $orderRepository;
        $this->orderWorkflowManager = $orderWorkflowManager;
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
        if (($payment->getState() !== $value || $payment->getState() === 'new') && PaymentInterface::STATE_UNKNOWN !== $value) {
            $this->updateOrderWorkflow($payment, $value);
        }
    }

    /**
     * @param PaymentInterface $payment
     * @param string $nextState
     */
    protected function updateOrderWorkflow(PaymentInterface $payment, $nextState)
    {
        $order = $this->orderRepository->find($payment->getOrderId());

        if (!$order instanceof OrderInterface) {
            return;
        }

        $params = null;
        if ($payment->getState() === PaymentInterface::STATE_PROCESSING || $payment->getState() === PaymentInterface::STATE_NEW) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PENDING_PAYMENT,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PENDING_PAYMENT,
            ];
        }
        else if ($payment->getState() === PaymentInterface::STATE_COMPLETED) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PROCESSING,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PROCESSING,
            ];
        }
        else if($payment->getState() === PaymentInterface::STATE_FAILED) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PAYMENT_REVIEW,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PAYMENT_REVIEW,
            ];
        }

        if (is_array($params)) {
            $this->orderWorkflowManager->changeState($order, 'change_order_state', $params);
        }
    }
}
