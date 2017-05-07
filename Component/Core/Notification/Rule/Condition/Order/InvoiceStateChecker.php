<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\Order;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;

class InvoiceStateChecker extends AbstractConditionChecker
{
    /**
     *
     */
    const INVOICE_TYPE_PARTIAL = 1;

    /**
     *
     */
    const INVOICE_TYPE_FULL = 2;

    /**
     *
     */
    const INVOICE_TYPE_ALL = 3;

    /**
     * @var ProcessableInterface
     */
    private $processableHelper;

    /**
     * @param ProcessableInterface $processableHelper
     */
    public function __construct(ProcessableInterface $processableHelper)
    {
        $this->processableHelper = $processableHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        $invoiceType = $configuration['invoiceType'];

        if ($subject instanceof OrderInterface) {
            $paramsToExist = [
                'invoice'
            ];

            foreach ($paramsToExist as $paramToExist) {
                if (!array_key_exists($paramToExist, $params)) {
                    return false;
                }
            }

            if ($invoiceType === self::INVOICE_TYPE_ALL) {
                return true;
            } elseif ($invoiceType === self::INVOICE_TYPE_FULL) {
                if (count($this->processableHelper->getProcessableItems($subject)) === 0) {
                    return true;
                }
            } elseif ($invoiceType === self::INVOICE_TYPE_PARTIAL) {
                if (count($this->processableHelper->getProcessableItems($subject)) > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}