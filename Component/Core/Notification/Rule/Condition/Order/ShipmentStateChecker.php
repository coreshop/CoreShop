<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\Order;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;

class ShipmentStateChecker extends AbstractConditionChecker
{
    /**
     *
     */
    const SHIPMENT_TYPE_PARTIAL = 1;

    /**
     *
     */
    const SHIPMENT_TYPE_FULL = 2;

    /**
     *
     */
    const SHIPMENT_TYPE_ALL = 3;

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
        $shipmentState = $configuration['shipmentState'];

        if ($subject instanceof OrderInterface) {
            if ($shipmentState === self::SHIPMENT_TYPE_ALL) {
                return true;
            } elseif ($shipmentState === self::SHIPMENT_TYPE_FULL) {
                if (count($this->processableHelper->getProcessableItems($subject)) === 0) {
                    return true;
                }
            } elseif ($shipmentState === self::SHIPMENT_TYPE_PARTIAL) {
                if (count($this->processableHelper->getProcessableItems($subject)) > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}