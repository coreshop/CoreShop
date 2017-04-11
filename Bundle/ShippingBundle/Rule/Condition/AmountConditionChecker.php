<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

class AmountConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $minAmount = $configuration['minAmount'];
        $maxAmount = $configuration['maxAmount'];
        $totalAmount = $cart->getSubtotal(true);

        if ($minAmount > 0) {
            if ($totalAmount <= $minAmount) {
                return false;
            }
        }

        if ($maxAmount > 0) {
            if ($totalAmount >= $maxAmount) {
                return false;
            }
        }

        return true;
    }
}
