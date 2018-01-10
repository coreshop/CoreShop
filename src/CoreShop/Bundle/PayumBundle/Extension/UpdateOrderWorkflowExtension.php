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

namespace CoreShop\Bundle\PayumBundle\Extension;

use CoreShop\Bundle\PayumBundle\Exception\ReplyException;
use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Request\Notify;

/**
 * Class UpdateOrderWorkflowExtension
 *
 * @deprecated remove after state machine is implemented
 * @package CoreShop\Bundle\PayumBundle\Extension
 */
final class UpdateOrderWorkflowExtension implements ExtensionInterface
{
    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var WorkflowManagerInterface
     */
    private $orderWorkflowManager;

    /**
     * @param PimcoreRepositoryInterface $orderRepository
     * @param WorkflowManagerInterface $orderWorkflowManager
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        PimcoreRepositoryInterface $orderRepository,
        WorkflowManagerInterface $orderWorkflowManager,
        CartRepositoryInterface $cartRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderWorkflowManager = $orderWorkflowManager;
        $this->cartRepository = $cartRepository;
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

        if (false === $request instanceof GetStatusInterface && false === $request instanceof Notify) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $order = $this->orderRepository->find($payment->getOrderId());

        if (!$order instanceof OrderInterface) {
            return;
        }

        $context->getGateway()->execute($status = new GetStatus($payment));
        $value = $status->getValue();
        if (($payment->getState() !== $value || $payment->getState() === 'new') && PaymentInterface::STATE_UNKNOWN !== $value) {
            try {
                $this->updateOrderWorkflow($order, $value);
            } catch (\Exception $ex) {
                $replyException = new ReplyException('reply', 0, $ex);


                $context->setReply($replyException);
                $context->setException($ex);

                throw $replyException;
            }
        }
    }

    /**
     * @param OrderInterface $order
     * @param string $nextState
     */
    private function updateOrderWorkflow(OrderInterface $order, $nextState)
    {
        $params = null;
        if ($nextState === PaymentInterface::STATE_PROCESSING || $nextState === PaymentInterface::STATE_NEW) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PENDING_PAYMENT,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PENDING_PAYMENT,
            ];
        } elseif ($nextState === PaymentInterface::STATE_COMPLETED) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PROCESSING,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PROCESSING,
            ];
        } elseif ($nextState === PaymentInterface::STATE_FAILED) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PAYMENT_REVIEW,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PAYMENT_REVIEW,
            ];
        } else if ($nextState === PaymentInterface::STATE_CANCELLED) {
            $this->cancelOrder($order);
        }

        if (is_array($params)) {
            $this->updateToState($order, $params);
        }
    }

    private function updateToState(OrderInterface $order, $state)
    {
        $this->orderWorkflowManager->changeState($order, 'change_order_state', $state);
    }

    private function cancelOrder(OrderInterface $order)
    {
        $cart = $this->cartRepository->findCartByOrder($order);

        if ($cart instanceof CartInterface) {
            $cart->setOrder(null);
            $cart->save();
        }

        $this->orderWorkflowManager->changeState($order, 'change_order_state', [
            'newState' => WorkflowManagerInterface::ORDER_STATUS_CANCELED,
            'newStatus' => WorkflowManagerInterface::ORDER_STATUS_CANCELED,
        ]);
    }
}
