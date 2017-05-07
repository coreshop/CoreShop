<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\Invoice;

use CoreShop\Component\Order\Model\OrderInvoiceInterface;

class InvoiceStateChecker extends \CoreShop\Component\Core\Notification\Rule\Condition\Order\InvoiceStateChecker
{
    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof OrderInvoiceInterface) {
            return parent::isNotificationRuleValid($subject->getOrder(), $params, $configuration);
        }

        return false;
    }
}