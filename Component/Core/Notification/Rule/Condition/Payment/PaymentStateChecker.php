<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\Payment;

use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

class PaymentStateChecker extends \CoreShop\Component\Core\Notification\Rule\Condition\Order\PaymentStateChecker
{
    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param PimcoreRepositoryInterface $orderRepository
     */
    public function __construct(PimcoreRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof PaymentInterface) {
            return parent::isNotificationRuleValid($this->orderRepository->find($subject->getOrderId()), $params, $configuration);
        }

        return false;
    }
}