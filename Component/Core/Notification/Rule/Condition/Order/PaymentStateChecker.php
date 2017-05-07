<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\Order;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderInterface;

class PaymentStateChecker extends AbstractConditionChecker
{
    /**
     *
     */
    const PAYMENT_TYPE_PARTIAL = 1;

    /**
     *
     */
    const PAYMENT_TYPE_FULL = 2;

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof OrderInterface) {
            if ($configuration['paymentState'] === self::PAYMENT_TYPE_FULL) {
                return $subject->getIsPayed();
            } elseif ($configuration['paymentState'] === self::PAYMENT_TYPE_PARTIAL) {
                $payments = $subject->getPayments();

                return count($payments) > 0;
            }
        }

        return false;
    }
}