<?php

namespace CoreShop\Bundle\OrderBundle\EventListener;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * TODO: Not sure if this is the right way of starting the workflow?
 */
final class PaymentPersistListener
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


    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PaymentInterface) {
            return;
        }

        $order = $this->orderRepository->find($entity->getOrderId());

        if (!$order instanceof OrderInterface) {
            return;
        }

        $params = null;
        if ($entity->getState() === PaymentInterface::STATE_PROCESSING || $entity->getState() === PaymentInterface::STATE_NEW) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PENDING_PAYMENT,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PENDING_PAYMENT,
            ];
        }
        else if ($entity->getState() === PaymentInterface::STATE_COMPLETED) {
            $params = [
                'newState' => WorkflowManagerInterface::ORDER_STATE_PROCESSING,
                'newStatus' => WorkflowManagerInterface::ORDER_STATUS_PROCESSING,
            ];
        }
        else if($entity->getState() === PaymentInterface::STATE_FAILED) {
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